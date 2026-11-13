<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\TrajetModel;

/**
 * Page d'accueil de l'application.
 */
class HomeController extends Controller
{
    /**
     * Affiche la liste des trajets disponibles.
     *
     * Les détails (auteur, contact) et les actions ne sont affichés
     * qu'aux utilisateurs connectés.
     *
     * @return string
     */
    public function index(): string
    {
        $trajetModel = new TrajetModel(Database::getConnection());

        return $this->render('home/index', [
            'trajets'  => $trajetModel->findDisponibles(),
            'connecte' => Auth::check(),
            'userId'   => Auth::id(),
        ]);
    }
}