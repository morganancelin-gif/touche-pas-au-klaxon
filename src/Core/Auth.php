<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Gestion de l'utilisateur connecté (stocké en session).
 */
final class Auth
{
    /**
     * Clé de stockage dans la session.
     */
    private const SESSION_KEY = '_user';

    /**
     * Connecte un utilisateur.
     *
     * L'identifiant de session est régénéré pour empêcher la fixation de session.
     *
     * @param array{id: int, nom: string, prenom: string, role: string} $user
     *
     * @return void
     */
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $user;
    }

    /**
     * Déconnecte l'utilisateur courant.
     *
     * @return void
     */
    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }

    /**
     * Retourne l'utilisateur connecté, ou null pour un visiteur.
     *
     * @return array{id: int, nom: string, prenom: string, role: string}|null
     */
    public static function user(): ?array
    {
        $user = $_SESSION[self::SESSION_KEY] ?? null;

        // Vérifie la structure des données de session avant de les utiliser
        if (
            !is_array($user)
            || !is_int($user['id'] ?? null)
            || !is_string($user['nom'] ?? null)
            || !is_string($user['prenom'] ?? null)
            || !is_string($user['role'] ?? null)
        ) {
            return null;
        }

        return [
        'id'     => $user['id'],
        'nom'    => $user['nom'],
        'prenom' => $user['prenom'],
        'role'   => $user['role'],
        ];
    }

    /**
     * Indique si un utilisateur est connecté.
     *
     * @return bool
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Retourne l'identifiant de l'utilisateur connecté, ou null.
     *
     * @return int|null
     */
    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    /**
     * Indique si l'utilisateur connecté est administrateur.
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? null) === 'admin';
    }
}
