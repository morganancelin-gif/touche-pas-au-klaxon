<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Auth;
use App\Core\Flash;

/**
 * Réserve une route aux administrateurs.
 */
class AdminMiddleware
{
    /**
     * Laisse passer un administrateur, sinon redirige avec un message.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (Auth::isAdmin()) {
            return true;
        }

        if (Auth::check()) {
            Flash::add('danger', 'Accès réservé aux administrateurs.');
            header('Location: /');
        } else {
            Flash::add('warning', 'Veuillez vous connecter pour accéder à cette page.');
            header('Location: /login');
        }
        exit;
    }
}