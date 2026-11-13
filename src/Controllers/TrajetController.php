<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use App\Models\TrajetModel;

/**
 * Gestion des trajets par les utilisateurs connectés.
 */
class TrajetController extends Controller
{
    /**
     * Supprime un trajet. Réservé à son auteur et aux administrateurs.
     *
     * @param int $id Identifiant du trajet (issu de l'URL).
     *
     * @return never
     */
    public function delete(int $id): never
    {
        $trajetModel = new TrajetModel(Database::getConnection());
        $trajet = $trajetModel->findById($id);

        if ($trajet === null) {
            Flash::add('danger', "Ce trajet n'existe pas.");
            $this->redirect('/');
        }

        // Contrôle côté serveur : masquer le bouton ne suffit pas.
        if (!$this->peutGerer($trajet)) {
            Flash::add('danger', "Vous n'êtes pas autorisé à supprimer ce trajet.");
            $this->redirect('/');
        }

        $trajetModel->delete($id);
        Flash::add('success', 'Le trajet a été supprimé.');
        $this->redirect('/');
    }

    /**
     * Indique si l'utilisateur connecté peut modifier ou supprimer ce trajet.
     *
     * @param array<string, mixed> $trajet
     *
     * @return bool
     */
    private function peutGerer(array $trajet): bool
    {
        return Auth::isAdmin() || (int) $trajet['id_employe'] === Auth::id();
    }
}