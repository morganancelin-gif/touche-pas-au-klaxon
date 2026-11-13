<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Démarrage sécurisé de la session PHP.
 */
final class Session
{
    /**
     * Démarre la session si elle ne l'est pas déjà, avec des cookies sécurisés.
     *
     * - httponly : le cookie n'est pas lisible en JavaScript (limite le vol de session par XSS) ;
     * - samesite=Lax : le cookie n'est pas envoyé par les formulaires d'autres sites (limite le CSRF) ;
     * - secure : le cookie ne circule qu'en HTTPS, quand le site est servi en HTTPS ;
     * - strict_mode : PHP refuse un identifiant de session qu'il n'a pas lui-même généré.
     *
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'cookie_secure'   => ($_SERVER['HTTPS'] ?? 'off') !== 'off',
            'use_strict_mode' => true,
        ]);
    }
}