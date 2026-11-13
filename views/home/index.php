<?php
/**
 * Page d'accueil : trajets à venir avec des places disponibles.
 *
 * Visiteur : liste simple.
 * Utilisateur connecté : bouton de détail (modale), et pour l'auteur
 * du trajet, boutons de modification et de suppression.
 *
 * @var list<array<string, mixed>> $trajets
 * @var bool                       $connecte
 * @var int|null                   $userId
 */

use App\Core\Csrf;

?>
<h1 class="h3 mb-3">
    <?= $connecte ? 'Trajets proposés' : "Pour obtenir plus d'informations sur un trajet, veuillez vous connecter" ?>
</h1>

<?php if ($trajets === []) : ?>
    <div class="alert alert-light border">Aucun trajet disponible pour le moment.</div>
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
                    <?php if ($connecte) : ?>
                        <th scope="col"><span class="visually-hidden">Actions</span></th>
                    <?php endif; ?>
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
                    <td><?= e($trajet['nb_places_disponibles']) ?></td>

                    <?php if ($connecte) : ?>
                        <td class="text-nowrap">
                            <button type="button" class="btn btn-link btn-sm text-dark p-1"
                                    data-bs-toggle="modal" data-bs-target="#trajet-<?= $id ?>"
                                    title="Voir le détail" aria-label="Voir le détail du trajet">
                                <?= icon('eye') ?>
                            </button>

                            <?php if ((int) $trajet['id_employe'] === $userId) : ?>
                                <a class="btn btn-link btn-sm text-dark p-1" href="/trajet/<?= $id ?>/modifier"
                                   title="Modifier" aria-label="Modifier le trajet">
                                    <?= icon('pencil-square') ?>
                                </a>

                                <form method="post" action="/trajet/<?= $id ?>/supprimer" class="d-inline"
                                      onsubmit="return confirm('Supprimer définitivement ce trajet ?');">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="btn btn-link btn-sm text-dark p-1"
                                            title="Supprimer" aria-label="Supprimer le trajet">
                                        <?= icon('trash') ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($connecte) : ?>
        <?php // Fenêtres modales de détail : générées uniquement pour un utilisateur connecté ?>
        <?php foreach ($trajets as $trajet) : ?>
            <?php $id = (int) $trajet['id_trajet']; ?>
            <div class="modal fade" id="trajet-<?= $id ?>" tabindex="-1"
                 aria-labelledby="trajet-<?= $id ?>-titre" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="trajet-<?= $id ?>-titre">
                                <?= e($trajet['agence_depart']) ?> → <?= e($trajet['agence_arrivee']) ?>
                            </h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <p>Auteur : <strong><?= e($trajet['auteur_prenom'] . ' ' . $trajet['auteur_nom']) ?></strong></p>
                            <p>Téléphone : <strong><?= e(formatTelephone((string) $trajet['auteur_telephone'])) ?></strong></p>
                            <p>Email : <strong><?= e($trajet['auteur_email']) ?></strong></p>
                            <p class="mb-0">Nombre total de places : <?= e($trajet['nb_places_total']) ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>