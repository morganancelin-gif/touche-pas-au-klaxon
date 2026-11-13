<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Protection contre les attaques CSRF (Cross-Site Request Forgery).
 *
 * Un jeton secret est stocké en session et ajouté dans chaque formulaire.
 * Une requête POST sans le bon jeton est rejetée : elle ne vient pas de notre site.
 */
final class Csrf
{
    /**
     * Clé de stockage dans la session.
     */
    private const SESSION_KEY = '_csrf_token';

    /**
     * Nom du champ caché dans les formulaires.
     */
    public const FIELD_NAME = '_csrf';

    /**
     * Retourne le jeton de la session, en le créant si nécessaire.
     *
     * @return string
     */
    public static function token(): string
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Retourne le champ caché HTML à placer dans chaque formulaire POST.
     *
     * @return string
     */
    public static function field(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            self::FIELD_NAME,
            htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Vérifie qu'un jeton reçu correspond à celui de la session.
     *
     * hash_equals() compare en temps constant, pour ne rien révéler du jeton
     * par la durée de la comparaison.
     *
     * @param mixed $token Jeton reçu (en général $_POST['_csrf']).
     *
     * @return bool
     */
    public static function isValid(mixed $token): bool
    {
        return is_string($token)
            && isset($_SESSION[self::SESSION_KEY])
            && is_string($_SESSION[self::SESSION_KEY])
            && hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}