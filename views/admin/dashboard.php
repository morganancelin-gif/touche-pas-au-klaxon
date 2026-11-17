<?php
/**
 * Tableau de bord administrateur.
 *
 * @var int $nbUtilisateurs
 * @var int $nbAgences
 * @var int $nbTrajets
 */

$sections = [
    ['titre' => 'Utilisateurs', 'nombre' => $nbUtilisateurs, 'lien' => '/admin/utilisateurs', 'texte' => 'Consulter la liste des employés'],
    ['titre' => 'Agences', 'nombre' => $nbAgences, 'lien' => '/admin/agences', 'texte' => 'Lister, créer, modifier et supprimer les agences'],
    ['titre' => 'Trajets', 'nombre' => $nbTrajets, 'lien' => '/admin/trajets', 'texte' => 'Consulter et supprimer les trajets'],
];
?>
<h1 class="h3 mb-4">Tableau de bord</h1>

<div class="row g-4">
    <?php foreach ($sections as $section) : ?>
        <div class="col-md-4">
            <div class="card h-100 border-dark-subtle">
                <div class="card-body">
                    <h2 class="h5 card-title"><?= e($section['titre']) ?></h2>
                    <p class="display-6 mb-2"><?= e($section['nombre']) ?></p>
                    <p class="card-text text-body-secondary"><?= e($section['texte']) ?></p>
                </div>
                <div class="card-footer bg-transparent border-0 pb-3">
                    <a href="<?= e($section['lien']) ?>" class="btn btn-secondary">Gérer</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>