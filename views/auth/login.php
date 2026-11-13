<?php
/**
 * Formulaire de connexion.
 *
 * @var string      $email Email saisi (réaffiché après une erreur).
 * @var string|null $error Message d'erreur, ou null.
 */

use App\Core\Csrf;

?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <h1 class="h3 mb-3">Connexion</h1>

        <?php if ($error !== null) : ?>
            <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/login">
            <?= Csrf::field() ?>

            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= e($email) ?>" required autofocus autocomplete="username">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password"
                       required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-dark w-100">Se connecter</button>
        </form>
    </div>
</div>