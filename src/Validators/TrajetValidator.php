<?php

declare(strict_types=1);

namespace App\Validators;

use DateTimeImmutable;

/**
 * Valide les données du formulaire de trajet (création et modification).
 *
 * Cette classe n'accède pas à la base de données : elle reçoit tout ce dont
 * elle a besoin en paramètre, ce qui la rend simple à tester unitairement.
 */
final class TrajetValidator
{
    /**
     * Format des champs <input type="datetime-local"> (ex. 2026-11-14T08:00).
     */
    public const FORMAT_FORMULAIRE = 'Y-m-d\TH:i';

    /**
     * Nombre maximal de places proposées dans un véhicule.
     */
    public const PLACES_MAX = 9;

    /**
     * Erreurs de la dernière validation, indexées par nom de champ.
     *
     * @var array<string, string>
     */
    private array $errors = [];

    /**
     * Valide les données saisies et les convertit au format de la base.
     *
     * @param array<string, string> $input          Données saisies (chaînes brutes du formulaire).
     * @param list<int>             $agencesValides Identifiants des agences existantes.
     * @param DateTimeImmutable     $maintenant     Date de référence (injectée pour faciliter les tests).
     *
     * @return array{id_agence_depart: int, id_agence_arrivee: int, date_heure_depart: string,
     *               date_heure_arrivee: string, nb_places_total: int, nb_places_disponibles: int}|null
     *         Les données prêtes à enregistrer, ou null si elles sont invalides (voir errors()).
     */
    public function validate(array $input, array $agencesValides, DateTimeImmutable $maintenant): ?array
    {
        $this->errors = [];

        // Agences (RG1 : départ et arrivée différents)
        $agenceDepart = $this->agence($input['agence_depart'] ?? '', $agencesValides, 'agence_depart');
        $agenceArrivee = $this->agence($input['agence_arrivee'] ?? '', $agencesValides, 'agence_arrivee');

        if ($agenceDepart !== null && $agenceDepart === $agenceArrivee) {
            $this->errors['agence_arrivee'] = "L'agence d'arrivée doit être différente de l'agence de départ.";
        }

        // Dates (départ dans le futur, RG2 : arrivée après le départ)
        $depart = $this->dateHeure($input['depart'] ?? '', 'depart');
        $arrivee = $this->dateHeure($input['arrivee'] ?? '', 'arrivee');

        if ($depart !== null && $depart <= $maintenant) {
            $this->errors['depart'] = 'Le départ doit être dans le futur.';
        }

        if ($depart !== null && $arrivee !== null && $arrivee <= $depart) {
            $this->errors['arrivee'] = "L'arrivée doit être postérieure au départ.";
        }

        // Places (RG3 : 1 <= total, 0 <= disponibles <= total)
        $total = $this->entier(
            $input['nb_places_total'] ?? '',
            1,
            'nb_places_total',
            'Le nombre total de places doit être compris entre 1 et ' . self::PLACES_MAX . '.'
        );
        $disponibles = $this->entier(
            $input['nb_places_disponibles'] ?? '',
            0,
            'nb_places_disponibles',
            'Le nombre de places disponibles doit être compris entre 0 et ' . self::PLACES_MAX . '.'
        );

        if ($total !== null && $disponibles !== null && $disponibles > $total) {
            $this->errors['nb_places_disponibles'] = 'Les places disponibles ne peuvent pas dépasser le nombre total de places.';
        }

        if (
            $this->errors !== []
            || $agenceDepart === null || $agenceArrivee === null
            || $depart === null || $arrivee === null
            || $total === null || $disponibles === null
        ) {
            return null;
        }

        return [
            'id_agence_depart'      => $agenceDepart,
            'id_agence_arrivee'     => $agenceArrivee,
            'date_heure_depart'     => $depart->format('Y-m-d H:i:s'),
            'date_heure_arrivee'    => $arrivee->format('Y-m-d H:i:s'),
            'nb_places_total'       => $total,
            'nb_places_disponibles' => $disponibles,
        ];
    }

    /**
     * Retourne les erreurs de la dernière validation.
     *
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Valide un identifiant d'agence : un entier qui correspond à une agence existante.
     *
     * @param string    $valeur         Valeur saisie.
     * @param list<int> $agencesValides Identifiants autorisés.
     * @param string    $champ          Nom du champ (pour l'erreur).
     *
     * @return int|null
     */
    private function agence(string $valeur, array $agencesValides, string $champ): ?int
    {
        $id = filter_var($valeur, FILTER_VALIDATE_INT);

        if ($id === false || !in_array($id, $agencesValides, true)) {
            $this->errors[$champ] = 'Veuillez choisir une agence dans la liste.';

            return null;
        }

        return $id;
    }

    /**
     * Valide une date et une heure au format du formulaire.
     *
     * @param string $valeur Valeur saisie (ex. 2026-11-14T08:00).
     * @param string $champ  Nom du champ (pour l'erreur).
     *
     * @return DateTimeImmutable|null
     */
    private function dateHeure(string $valeur, string $champ): ?DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!' . self::FORMAT_FORMULAIRE, $valeur);

        // createFromFormat() accepte un 30 février (reporté au 2 mars) :
        // on vérifie que la date relue est identique à la saisie.
        if ($date === false || $date->format(self::FORMAT_FORMULAIRE) !== $valeur) {
            $this->errors[$champ] = 'Veuillez saisir une date et une heure valides.';

            return null;
        }

        return $date;
    }

    /**
     * Valide un nombre entier compris entre $min et PLACES_MAX.
     *
     * @param string $valeur  Valeur saisie.
     * @param int    $min     Valeur minimale acceptée.
     * @param string $champ   Nom du champ (pour l'erreur).
     * @param string $message Message d'erreur.
     *
     * @return int|null
     */
    private function entier(string $valeur, int $min, string $champ, string $message): ?int
    {
        $nombre = filter_var($valeur, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => self::PLACES_MAX],
        ]);

        if ($nombre === false) {
            $this->errors[$champ] = $message;

            return null;
        }

        return $nombre;
    }
}
