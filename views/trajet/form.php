<?php
/**
 * Formulaire de création et de modification d'un trajet.
 *
 * @var string                     $titre   Titre de la page.
 * @var string                     $action  URL de soumission.
 * @var array<string, string>      $valeurs Valeurs des champs.
 * @var array<string, string>      $erreurs Erreurs par champ (+ "global").
 * @var array<string, mixed>|null  $auteur  Auteur du trajet (informations non modifiables).
 * @var list<array<string, mixed>> $agences Agences disponibles.
 */

use App\Core\Csrf;
use App\Validators\TrajetValidator;

/**
 * Classe Bootstrap "is-invalid" si le champ est en erreur.
 */
$invalide = static fn (string $champ): string => isset($erreurs[$champ]) ? ' is-invalid' : '';

/**
 * Message d'erreur sous le champ, s'il y en a un.
 */
$erreur = static fn (string $champ): string => isset($erreurs[$champ])
    ? '<div class="invalid-feedback">' . e($erreurs[$champ]) . '</div>'
    : '';
?>
<h1 class="h3 mb-3"><?= e($titre) ?></h1>

<?php if (isset($erreurs['global'])) : ?>
    <div class="alert alert-danger" role="alert"><?= e($erreurs['global']) ?></div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" novalidate>
    <?= Csrf::field() ?>

    <!-- Auteur : pré-rempli et non modifiable (champs désactivés, jamais envoyés) -->
    <fieldset class="border rounded-3 p-3 mb-4">
        <legend class="float-none w-auto px-2 fs-6 mb-0">Personne à contacter</legend>
        <div class="row g-3">
            <div class="col-md-3">
                <label for="auteur-nom" class="form-label">Nom</label>
                <input type="text" id="auteur-nom" class="form-control" value="<?= e($auteur['nom'] ?? '') ?>" disabled>
            </div>
            <div class="col-md-3">
                <label for="auteur-prenom" class="form-label">Prénom</label>
                <input type="text" id="auteur-prenom" class="form-control" value="<?= e($auteur['prenom'] ?? '') ?>" disabled>
            </div>
            <div class="col-md-3">
                <label for="auteur-email" class="form-label">Email</label>
                <input type="email" id="auteur-email" class="form-control" value="<?= e($auteur['email'] ?? '') ?>" disabled>
            </div>
            <div class="col-md-3">
                <label for="auteur-telephone" class="form-label">Téléphone</label>
                <input type="text" id="auteur-telephone" class="form-control"
                       value="<?= e(formatTelephone((string) ($auteur['telephone'] ?? ''))) ?>" disabled>
            </div>
        </div>
    </fieldset>

    <div class="row g-4 mb-4">
        <!-- Départ -->
        <div class="col-md-6">
            <fieldset class="border rounded-3 p-3 h-100">
                <legend class="float-none w-auto px-2 fs-6 mb-0">Départ</legend>

                <div class="mb-3">
                    <label for="agence_depart" class="form-label">Agence de départ</label>
                    <select id="agence_depart" name="agence_depart" class="form-select<?= $invalide('agence_depart') ?>" required>
                        <option value="">Choisir une agence</option>
                        <?php foreach ($agences as $agence) : ?>
                            <option value="<?= e($agence['id_agence']) ?>"
                                <?= (string) $agence['id_agence'] === $valeurs['agence_depart'] ? 'selected' : '' ?>>
                                <?= e($agence['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= $erreur('agence_depart') ?>
                </div>

                <div>
                    <label for="depart" class="form-label">Date et heure de départ</label>
                    <input type="datetime-local" id="depart" name="depart" class="form-control<?= $invalide('depart') ?>"
                           value="<?= e($valeurs['depart']) ?>" required>
                    <?= $erreur('depart') ?>
                </div>
            </fieldset>
        </div>

        <!-- Arrivée -->
        <div class="col-md-6">
            <fieldset class="border rounded-3 p-3 h-100">
                <legend class="float-none w-auto px-2 fs-6 mb-0">Arrivée</legend>

                <div class="mb-3">
                    <label for="agence_arrivee" class="form-label">Agence d'arrivée</label>
                    <select id="agence_arrivee" name="agence_arrivee" class="form-select<?= $invalide('agence_arrivee') ?>" required>
                        <option value="">Choisir une agence</option>
                        <?php foreach ($agences as $agence) : ?>
                            <option value="<?= e($agence['id_agence']) ?>"
                                <?= (string) $agence['id_agence'] === $valeurs['agence_arrivee'] ? 'selected' : '' ?>>
                                <?= e($agence['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= $erreur('agence_arrivee') ?>
                </div>

                <div>
                    <label for="arrivee" class="form-label">Date et heure d'arrivée</label>
                    <input type="datetime-local" id="arrivee" name="arrivee" class="form-control<?= $invalide('arrivee') ?>"
                           value="<?= e($valeurs['arrivee']) ?>" required>
                    <?= $erreur('arrivee') ?>
                </div>
            </fieldset>
        </div>
    </div>

    <!-- Places -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="nb_places_total" class="form-label">Nombre total de places</label>
            <input type="number" id="nb_places_total" name="nb_places_total" min="1" max="<?= TrajetValidator::PLACES_MAX ?>"
                   class="form-control<?= $invalide('nb_places_total') ?>" value="<?= e($valeurs['nb_places_total']) ?>" required>
            <?= $erreur('nb_places_total') ?>
        </div>
        <div class="col-md-3">
            <label for="nb_places_disponibles" class="form-label">Places disponibles</label>
            <input type="number" id="nb_places_disponibles" name="nb_places_disponibles" min="0" max="<?= TrajetValidator::PLACES_MAX ?>"
                   class="form-control<?= $invalide('nb_places_disponibles') ?>" value="<?= e($valeurs['nb_places_disponibles']) ?>" required>
            <?= $erreur('nb_places_disponibles') ?>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-dark">Enregistrer</button>
        <a href="/" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>