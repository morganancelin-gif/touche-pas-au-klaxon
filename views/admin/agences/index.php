<?php
/**
 * Liste des agences, avec création, modification et suppression.
 *
 * @var list<array<string, mixed>> $agences
 */

use App\Core\Csrf;

?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Agences</h1>
    <a href="/admin/agences/creer" class="btn btn-dark">Créer une agence</a>
</div>

<?php if ($agences === []) : ?>
    <div class="alert alert-light border">Aucune agence enregistrée.</div>
<?php else : ?>
    <div class="rounded-3 overflow-hidden border">
        <table class="table table-striped table-bordered table-trajets align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Ville</th>
                    <th scope="col" class="text-center">Trajets liés</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($agences as $agence) : ?>
                <?php $id = (int) $agence['id_agence']; ?>
                <tr>
                    <td><?= e($agence['nom']) ?></td>
                    <td class="text-center"><?= e($agence['nb_trajets']) ?></td>
                    <td class="text-center text-nowrap">
                        <a class="btn btn-link btn-sm text-dark p-1" href="/admin/agences/<?= $id ?>/modifier"
                           title="Modifier" aria-label="Modifier l'agence <?= e($agence['nom']) ?>">
                            <?= icon('pencil-square') ?>
                        </a>
                        <form method="post" action="/admin/agences/<?= $id ?>/supprimer" class="d-inline"
                              onsubmit="return confirm('Supprimer définitivement cette agence ?');">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn btn-link btn-sm text-dark p-1"
                                    title="Supprimer" aria-label="Supprimer l'agence <?= e($agence['nom']) ?>">
                                <?= icon('trash') ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>