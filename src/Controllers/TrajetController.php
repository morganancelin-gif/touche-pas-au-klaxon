<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use App\Models\AgenceModel;
use App\Models\EmployeModel;
use App\Models\TrajetModel;
use App\Validators\TrajetValidator;
use DateTimeImmutable;
use PDOException;

/**
 * Gestion des trajets par les utilisateurs connectés :
 * création, modification et suppression.
 */
class TrajetController extends Controller
{
    /**
     * Champs du formulaire de trajet.
     */
    private const CHAMPS = [
        'agence_depart',
        'agence_arrivee',
        'depart',
        'arrivee',
        'nb_places_total',
        'nb_places_disponibles',
    ];

    /**
     * Affiche le formulaire de création.
     *
     * @return string
     */
    public function create(): string
    {
        return $this->renderForm(
            'Créer un trajet',
            '/trajet/creer',
            array_fill_keys(self::CHAMPS, ''),
            [],
            (int) Auth::id()
        );
    }

    /**
     * Traite le formulaire de création.
     *
     * @return string Le formulaire avec ses erreurs, en cas d'échec.
     */
    public function store(): string
    {
        $input = $this->formInput();
        $validator = new TrajetValidator();
        $data = $validator->validate($input, $this->agencesValides(), new DateTimeImmutable());

        if ($data === null) {
            return $this->renderForm('Créer un trajet', '/trajet/creer', $input, $validator->errors(), (int) Auth::id());
        }

        try {
            // L'auteur vient de la session, jamais du formulaire.
            (new TrajetModel(Database::getConnection()))->create($data + ['id_employe' => (int) Auth::id()]);
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return $this->renderForm(
                'Créer un trajet',
                '/trajet/creer',
                $input,
                ['global' => "L'enregistrement a échoué. Merci de réessayer."],
                (int) Auth::id()
            );
        }

        Flash::add('success', 'Le trajet a été créé.');
        $this->redirect('/');
    }

    /**
     * Affiche le formulaire de modification, pré-rempli.
     *
     * @param int $id Identifiant du trajet (issu de l'URL).
     *
     * @return string
     */
    public function edit(int $id): string
    {
        $trajet = $this->trajetGerable($id, 'modifier');

        $valeurs = [
            'agence_depart'         => (string) $trajet['id_agence_depart'],
            'agence_arrivee'        => (string) $trajet['id_agence_arrivee'],
            'depart'                => $this->versFormulaire((string) $trajet['date_heure_depart']),
            'arrivee'               => $this->versFormulaire((string) $trajet['date_heure_arrivee']),
            'nb_places_total'       => (string) $trajet['nb_places_total'],
            'nb_places_disponibles' => (string) $trajet['nb_places_disponibles'],
        ];

        return $this->renderForm('Modifier le trajet', "/trajet/{$id}/modifier", $valeurs, [], (int) $trajet['id_employe']);
    }

    /**
     * Traite le formulaire de modification.
     *
     * @param int $id Identifiant du trajet (issu de l'URL).
     *
     * @return string Le formulaire avec ses erreurs, en cas d'échec.
     */
    public function update(int $id): string
    {
        $trajet = $this->trajetGerable($id, 'modifier');
        $auteurId = (int) $trajet['id_employe'];
        $action = "/trajet/{$id}/modifier";

        $input = $this->formInput();
        $validator = new TrajetValidator();
        $data = $validator->validate($input, $this->agencesValides(), new DateTimeImmutable());

        if ($data === null) {
            return $this->renderForm('Modifier le trajet', $action, $input, $validator->errors(), $auteurId);
        }

        try {
            (new TrajetModel(Database::getConnection()))->update($id, $data);
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return $this->renderForm(
                'Modifier le trajet',
                $action,
                $input,
                ['global' => "L'enregistrement a échoué. Merci de réessayer."],
                $auteurId
            );
        }

        Flash::add('success', 'Le trajet a été modifié.');
        $this->redirect('/');
    }

    /**
     * Supprime un trajet. Réservé à son auteur et aux administrateurs.
     *
     * @param int $id Identifiant du trajet (issu de l'URL).
     *
     * @return never
     */
    public function delete(int $id): never
    {
        $this->trajetGerable($id, 'supprimer');

        (new TrajetModel(Database::getConnection()))->delete($id);
        Flash::add('success', 'Le trajet a été supprimé.');
        $this->redirect('/');
    }

    /**
     * Charge un trajet que l'utilisateur connecté a le droit de gérer.
     *
     * Redirige vers l'accueil avec un message si le trajet n'existe pas
     * ou si l'utilisateur n'en est ni l'auteur ni un administrateur.
     * Ce contrôle côté serveur est indispensable : masquer un bouton ne suffit pas.
     *
     * @param int    $id     Identifiant du trajet.
     * @param string $action Verbe pour le message d'erreur (modifier, supprimer).
     *
     * @return array<string, mixed>
     */
    private function trajetGerable(int $id, string $action): array
    {
        $trajet = (new TrajetModel(Database::getConnection()))->findById($id);

        if ($trajet === null) {
            Flash::add('danger', "Ce trajet n'existe pas.");
            $this->redirect('/');
        }

        if (!Auth::isAdmin() && (int) $trajet['id_employe'] !== Auth::id()) {
            Flash::add('danger', "Vous n'êtes pas autorisé à {$action} ce trajet.");
            $this->redirect('/');
        }

        return $trajet;
    }

    /**
     * Affiche le formulaire de trajet (création ou modification).
     *
     * @param string                $titre    Titre de la page.
     * @param string                $action   URL de soumission du formulaire.
     * @param array<string, string> $valeurs  Valeurs des champs.
     * @param array<string, string> $erreurs  Erreurs, indexées par nom de champ.
     * @param int                   $auteurId Identifiant de l'auteur du trajet.
     *
     * @return string
     */
    private function renderForm(string $titre, string $action, array $valeurs, array $erreurs, int $auteurId): string
    {
        $pdo = Database::getConnection();

        return $this->render('trajet/form', [
            'titre'   => $titre,
            'action'  => $action,
            'valeurs' => $valeurs,
            'erreurs' => $erreurs,
            'auteur'  => (new EmployeModel($pdo))->findById($auteurId),
            'agences' => (new AgenceModel($pdo))->findAll(),
        ]);
    }

    /**
     * Lit les champs du formulaire de trajet.
     *
     * @return array<string, string>
     */
    private function formInput(): array
    {
        $input = [];
        foreach (self::CHAMPS as $champ) {
            $input[$champ] = $this->input($champ);
        }

        return $input;
    }

    /**
     * Retourne les identifiants des agences existantes.
     *
     * @return list<int>
     */
    private function agencesValides(): array
    {
        $agences = (new AgenceModel(Database::getConnection()))->findAll();

        return array_map(static fn (array $agence): int => (int) $agence['id_agence'], $agences);
    }

    /**
     * Convertit une date SQL (2026-11-14 08:00:00) au format du formulaire (2026-11-14T08:00).
     *
     * @param string $datetime
     *
     * @return string
     */
    private function versFormulaire(string $datetime): string
    {
        return (new DateTimeImmutable($datetime))->format(TrajetValidator::FORMAT_FORMULAIRE);
    }
}