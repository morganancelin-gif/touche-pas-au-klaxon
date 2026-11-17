<?php
/**
 * Liste de tous les trajets, avec suppression.
 *
 * @var list<array<string, mixed>> $trajets
 */

use App\Core\Csrf;

$maintenant = new DateTimeImmutable();
?>
<h1 class="h3 mb-3">Trajets</h1>

<?php if ($trajets === []) : ?>
    <div class="alert alert-light border">Aucun trajet enregistré.</div>
<?php else : ?>
    <div class="rounded-3 overflow-hidden border">
        <table class="table table-striped table-bordered table-trajets text-center align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Départ</th>
                    <th scope="col">Date</th>
                    <th scope="col">Heure</th>
                    <th scope="col">Destination</th>
                    <th scope="col">Date</th>
                    <th scope="col">Heure</th>
                    <th scope="col">Places</th>
                    <th scope="col">Auteur</th>
                    <th scope="col">Statut</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($trajets as $trajet) : ?>
                <?php $id = (int) $trajet['id_trajet']; ?>
                <tr>
                    <td><?= e($trajet['agence_depart']) ?></td>
                    <td><?= e(formatDate($trajet['date_heure_depart'])) ?></td>
                    <td><?= e(formatHeure($trajet['date_heure_depart'])) ?></td>
                    <td><?= e($trajet['agence_arrivee']) ?></td>
                    <td><?= e(formatDate($trajet['date_heure_arrivee'])) ?></td>
                    <td><?= e(formatHeure($trajet['date_heure_arrivee'])) ?></td>
                    <td><?= e($trajet['nb_places_disponibles']) ?> / <?= e($trajet['nb_places_total']) ?></td>
                    <td><?= e($trajet['auteur_prenom'] . ' ' . $trajet['auteur_nom']) ?></td>
                    <td>
                        <?php if (new DateTimeImmutable((string) $trajet['date_heure_depart']) <= $maintenant) : ?>
                            <span class="badge text-bg-secondary">Passé</span>
                        <?php elseif ((int) $trajet['nb_places_disponibles'] === 0) : ?>
                            <span class="badge text-bg-danger">Complet</span>
                        <?php else : ?>
                            <span class="badge text-bg-success">Ouvert</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post" action="/admin/trajets/<?= $id ?>/supprimer" class="d-inline"
                              onsubmit="return confirm('Supprimer définitivement ce trajet ?');">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn btn-link btn-sm text-dark p-1"
                                    title="Supprimer" aria-label="Supprimer le trajet">
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