<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Messages flash : stockés en session, affichés une seule fois
 * sur la page suivante (pattern Post/Redirect/Get).
 */
final class Flash
{
    /**
     * Clé de stockage dans la session.
     */
    private const SESSION_KEY = '_flash';

    /**
     * Ajoute un message à afficher sur la prochaine page.
     *
     * @param string $type    Type Bootstrap de l'alerte : success, danger, warning ou info.
     * @param string $message Texte du message.
     *
     * @return void
     */
    public static function add(string $type, string $message): void
    {
        $_SESSION[self::SESSION_KEY][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Retourne les messages en attente puis les supprime de la session.
     *
     * @return list<array{type: string, message: string}>
     */
    public static function consume(): array
    {
        $messages = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);

        return is_array($messages) ? array_values($messages) : [];
    }
}