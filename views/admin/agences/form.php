<?php
/**
 * Formulaire de création et de modification d'une agence.
 *
 * @var string      $titre  Titre de la page.
 * @var string      $action URL de soumission.
 * @var string      $nom    Valeur du champ.
 * @var string|null $erreur Message d'erreur, ou null.
 */

use App\Core\Csrf;
use App\Validators\AgenceValidator;

?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="h3 mb-3"><?= e($titre) ?></h1>

        <form method="post" action="<?= e($action) ?>" novalidate>
            <?= Csrf::field() ?>

            <div class="mb-3">
                <label for="nom" class="form-label">Ville</label>
                <input type="text" id="nom" name="nom" class="form-control<?= $erreur !== null ? ' is-invalid' : '' ?>"
                       value="<?= e($nom) ?>" maxlength="<?= AgenceValidator::LONGUEUR_MAX ?>" required autofocus>
                <?php if ($erreur !== null) : ?>
                    <div class="invalid-feedback"><?= e($erreur) ?></div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark">Enregistrer</button>
                <a href="/admin/agences" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>