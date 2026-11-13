<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Csrf;

/**
 * Vérifie le jeton CSRF de toute requête POST.
 */
class CsrfMiddleware
{
    /**
     * Laisse passer une requête POST dont le jeton est valide, sinon la rejette (403).
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (Csrf::isValid($_POST[Csrf::FIELD_NAME] ?? null)) {
            return true;
        }

        http_response_code(403);
        echo '<h1>Requête refusée</h1>'
            . '<p>Le formulaire a expiré ou ne provient pas de ce site. '
            . '<a href="/">Retour à l\'accueil</a></p>';
        exit;
    }
}