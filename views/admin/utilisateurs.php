<?php
/**
 * Liste des utilisateurs (lecture seule).
 *
 * @var list<array<string, mixed>> $utilisateurs
 */
?>
<h1 class="h3 mb-3">Utilisateurs</h1>
<p class="text-body-secondary">Les employés sont gérés par le service RH : la liste est en lecture seule.</p>

<div class="rounded-3 overflow-hidden border">
    <table class="table table-striped table-bordered table-trajets align-middle mb-0">
        <thead class="table-dark">
            <tr>
                <th scope="col">Nom</th>
                <th scope="col">Prénom</th>
                <th scope="col">Téléphone</th>
                <th scope="col">Email</th>
                <th scope="col" class="text-center">Rôle</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($utilisateurs as $utilisateur) : ?>
            <tr>
                <td><?= e($utilisateur['nom']) ?></td>
                <td><?= e($utilisateur['prenom']) ?></td>
                <td><?= e(formatTelephone((string) $utilisateur['telephone'])) ?></td>
                <td><?= e($utilisateur['email']) ?></td>
                <td class="text-center">
                    <?php if ($utilisateur['role'] === 'admin') : ?>
                        <span class="badge text-bg-primary">Administrateur</span>
                    <?php else : ?>
                        <span class="badge text-bg-light border">Utilisateur</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>