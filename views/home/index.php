<?php
/**
 * Page d'accueil : trajets à venir avec des places disponibles.
 *
 * @var list<array<string, mixed>> $trajets
 */
?>
<h1>Pour obtenir plus d'informations sur un trajet, veuillez vous connecter</h1>

<?php if ($trajets === []) : ?>
    <p>Aucun trajet disponible pour le moment.</p>
<?php else : ?>
    <table>
        <thead>
            <tr>
                <th>Départ</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Destination</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Places</th>
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
<?php endif; ?>