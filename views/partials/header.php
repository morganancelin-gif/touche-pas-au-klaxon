<?php
/**
 * En-tête commun à toutes les pages, adapté au profil :
 * visiteur, utilisateur connecté ou administrateur.
 */

use App\Core\Auth;
use App\Core\Csrf;

$user = Auth::user();
?>
<header class="container mt-3">
    <nav class="navbar app-header bg-light rounded-3 px-3">
        <a class="navbar-brand fw-semibold" href="<?= Auth::isAdmin() ? '/admin' : '/' ?>">Touche pas au klaxon</a>

        <div class="d-flex align-items-center gap-2">
            <?php if ($user === null) : ?>
                <a class="btn btn-dark" href="/login">Connexion</a>
            <?php else : ?>
                <?php if (Auth::isAdmin()) : ?>
                    <a class="btn btn-secondary" href="/admin/utilisateurs">Utilisateurs</a>
                    <a class="btn btn-secondary" href="/admin/agences">Agences</a>
                    <a class="btn btn-secondary" href="/admin/trajets">Trajets</a>
                <?php else : ?>
                    <a class="btn btn-dark" href="/trajet/creer">Créer un trajet</a>
                <?php endif; ?>

                <span class="mx-2">Bonjour <?= e($user['prenom'] . ' ' . $user['nom']) ?></span>

                <form method="post" action="/logout" class="m-0">
                    <?= Csrf::field() ?>
                    <button type="submit" class="btn btn-dark">Déconnexion</button>
                </form>
            <?php endif; ?>
        </div>
    </nav>
</header>