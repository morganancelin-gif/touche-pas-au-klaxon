<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use App\Models\AgenceModel;
use App\Models\EmployeModel;
use App\Models\TrajetModel;

/**
 * Tableau de bord administrateur : utilisateurs et trajets.
 *
 * Toutes les routes de ce contrôleur sont protégées par AdminMiddleware
 * (voir le groupe /admin dans config/routes.php).
 */
class AdminController extends Controller
{
    /**
     * Page d'accueil du tableau de bord.
     *
     * @return string
     */
    public function dashboard(): string
    {
        $pdo = Database::getConnection();

        return $this->render('admin/dashboard', [
            'nbUtilisateurs' => count((new EmployeModel($pdo))->findAll()),
            'nbAgences'      => count((new AgenceModel($pdo))->findAll()),
            'nbTrajets'      => count((new TrajetModel($pdo))->findAll()),
        ]);
    }

    /**
     * Liste des utilisateurs (lecture seule : les données viennent du SI RH).
     *
     * @return string
     */
    public function utilisateurs(): string
    {
        return $this->render('admin/utilisateurs', [
            'utilisateurs' => (new EmployeModel(Database::getConnection()))->findAll(),
        ]);
    }

    /**
     * Liste de tous les trajets, passés et complets compris.
     *
     * @return string
     */
    public function trajets(): string
    {
        return $this->render('admin/trajets', [
            'trajets' => (new TrajetModel(Database::getConnection()))->findAll(),
        ]);
    }

    /**
     * Supprime un trajet, puis revient à la liste des trajets.
     *
     * @param int $id Identifiant du trajet (issu de l'URL).
     *
     * @return never
     */
    public function supprimerTrajet(int $id): never
    {
        $supprime = (new TrajetModel(Database::getConnection()))->delete($id);

        if ($supprime) {
            Flash::add('success', 'Le trajet a été supprimé.');
        } else {
            Flash::add('danger', "Ce trajet n'existe pas.");
        }

        $this->redirect('/admin/trajets');
    }
}
