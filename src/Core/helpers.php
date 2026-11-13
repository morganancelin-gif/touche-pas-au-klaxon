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
}