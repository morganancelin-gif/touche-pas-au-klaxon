<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Valide le nom d'une agence (ville).
 *
 * Comme TrajetValidator, cette classe n'accède pas à la base :
 * l'unicité du nom est vérifiée par le contrôleur via le modèle.
 */
final class AgenceValidator
{
    /**
     * Longueur maximale du nom (taille de la colonne agence.nom).
     */
    public const LONGUEUR_MAX = 100;

    /**
     * Motif autorisé : lettres (accentuées comprises), espaces, apostrophes et tirets,
     * en commençant et en finissant par une lettre.
     */
    private const MOTIF = '/^\p{L}([\p{L} \'\-]*\p{L})?$/u';

    /**
     * Erreur de la dernière validation, ou null.
     *
     * @var string|null
     */
    private ?string $error = null;

    /**
     * Valide et normalise un nom d'agence.
     *
     * Accepte les lettres (accentuées comprises), les espaces, les tirets
     * et les apostrophes : "Saint-Étienne", "L'Isle-d'Abeau".
     *
     * @param string $nom Nom saisi.
     *
     * @return string|null Le nom normalisé (espaces superflus retirés, majuscule initiale),
     *                     ou null s'il est invalide.
     */
    public function validate(string $nom): ?string
    {
        $this->error = null;

        // Remplace les suites d'espaces par un seul espace
        $nom = trim((string) preg_replace('/\s+/u', ' ', $nom));

        if ($nom === '') {
            $this->error = "Le nom de l'agence est obligatoire.";

            return null;
        }

        if (mb_strlen($nom) > self::LONGUEUR_MAX) {
            $this->error = 'Le nom ne doit pas dépasser ' . self::LONGUEUR_MAX . ' caractères.';

            return null;
        }

        // Commence et finit par une lettre ; au milieu : lettres, espaces, ' et -
        if (preg_match(self::MOTIF, $nom) !== 1) {
            $this->error = 'Le nom ne peut contenir que des lettres, des espaces, des tirets et des apostrophes.';

            return null;
        }

        // Majuscule initiale : "saint-Étienne" devient "Saint-Étienne"
        return mb_strtoupper(mb_substr($nom, 0, 1)) . mb_substr($nom, 1);
    }

    /**
     * Retourne l'erreur de la dernière validation, ou null.
     *
     * @return string|null
     */
    public function error(): ?string
    {
        return $this->error;
    }
}
