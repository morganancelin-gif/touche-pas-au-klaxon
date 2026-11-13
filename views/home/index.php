<?php
/**
 * Page d'accueil : trajets à venir avec des places disponibles.
 *
 * @var list<array<string, mixed>> $trajets
 */
?>
<h1 class="h3 mb-3">Pour obtenir plus d'informations sur un trajet, veuillez vous connecter</h1>

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
                </tr>
            </thead>
            <tbody>
            <?php foreach ($trajets as $trajet) : ?>
                <tr>
                    <td><?= e($trajet['agence_depart']) ?></td>
                    <td><?= e(formatDate($trajet['date_heure_depart'])) ?></td>
                    <td><?= e(formatHeure($trajet['date_heure_depart'])) ?></td>
                    <td><?= e($trajet['agence_arrivee']) ?></td>
                    <td><?= e(formatDate($trajet['date_heure_arrivee'])) ?></td>
                    <td><?= e(formatHeure($trajet['date_heure_arrivee'])) ?></td>
                    <td><?= e($trajet['nb_places_disponibles']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>