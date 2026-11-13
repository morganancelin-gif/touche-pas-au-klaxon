<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Auth;
use App\Core\Flash;

/**
 * Réserve une route aux utilisateurs connectés.
 */
class AuthMiddleware
{
    /**
     * Laisse passer un utilisateur connecté, sinon redirige vers la connexion.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (Auth::check()) {
            return true;
        }

        Flash::add('warning', 'Veuillez vous connecter pour accéder à cette page.');
        header('Location: /login');
        exit;
    }
}