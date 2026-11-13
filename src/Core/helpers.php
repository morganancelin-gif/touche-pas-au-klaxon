<?php

declare(strict_types=1);

/**
 * Fonctions utilitaires disponibles partout (chargées par Composer).
 */

if (!function_exists('e')) {
    /**
     * Échappe une valeur pour l'afficher sans risque dans du HTML (protection XSS).
     *
     * @param string|int|float|null $value Valeur à afficher.
     *
     * @return string
     */
    function e(string|int|float|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('formatDate')) {
    /**
     * Formate une date SQL (DATETIME) au format jj/mm/aa.
     *
     * @param string $datetime Date au format "AAAA-MM-JJ HH:MM:SS".
     *
     * @return string
     */
    function formatDate(string $datetime): string
    {
        return (new DateTimeImmutable($datetime))->format('d/m/y');
    }
}

if (!function_exists('formatHeure')) {
    /**
     * Formate l'heure d'une date SQL (DATETIME) au format HH:MM.
     *
     * @param string $datetime Date au format "AAAA-MM-JJ HH:MM:SS".
     *
     * @return string
     */
    function formatHeure(string $datetime): string
    {
        return (new DateTimeImmutable($datetime))->format('H:i');
    }


if (!function_exists('formatTelephone')) {
    /**
     * Formate un numéro de téléphone français par paires : 06 12 34 56 78.
     *
     * @param string $telephone Numéro brut (ex. "0612345678").
     *
     * @return string Le numéro formaté, ou tel quel s'il ne fait pas 10 chiffres.
     */
    function formatTelephone(string $telephone): string
    {
        $chiffres = preg_replace('/\D/', '', $telephone) ?? '';

        return strlen($chiffres) === 10 ? trim(chunk_split($chiffres, 2, ' ')) : $telephone;
    }
}

if (!function_exists('icon')) {
    /**
     * Retourne le code SVG d'une icône du dossier views/icons (icônes Bootstrap Icons).
     *
     * @param string $name Nom de l'icône, sans extension (ex. "eye").
     *
     * @return string Le SVG, ou une chaîne vide si l'icône n'existe pas.
     */
    function icon(string $name): string
    {
        // basename() empêche de sortir du dossier (ex. "../../.env").
        $file = __DIR__ . '/../../views/icons/' . basename($name) . '.svg';

        if (!is_file($file)) {
            return '';
        }

        // Icône décorative : masquée aux lecteurs d'écran (le bouton porte un aria-label).
        return str_replace('<svg ', '<svg aria-hidden="true" ', (string) file_get_contents($file));
    }
}
}