<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use App\Models\AgenceModel;
use App\Validators\AgenceValidator;
use PDOException;

/**
 * Gestion des agences par l'administrateur : liste, création, modification, suppression.
 *
 * Toutes les routes de ce contrôleur sont protégées par AdminMiddleware.
 */
class AgenceController extends Controller
{
    /**
     * Liste des agences.
     *
     * @return string
     */
    public function index(): string
    {
        return $this->render('admin/agences/index', [
            'agences' => (new AgenceModel(Database::getConnection()))->findAllAvecNbTrajets(),
        ]);
    }

    /**
     * Formulaire de création.
     *
     * @return string
     */
    public function create(): string
    {
        return $this->renderForm('Créer une agence', '/admin/agences/creer', '', null);
    }

    /**
     * Traite le formulaire de création.
     *
     * @return string Le formulaire avec son erreur, en cas d'échec.
     */
    public function store(): string
    {
        $saisie = $this->input('nom');
        $agenceModel = new AgenceModel(Database::getConnection());
        $nom = $this->valider($saisie, $agenceModel, null, $erreur);

        if ($nom === null) {
            return $this->renderForm('Créer une agence', '/admin/agences/creer', $saisie, $erreur);
        }

        try {
            $agenceModel->create($nom);
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return $this->renderForm(
                'Créer une agence',
                '/admin/agences/creer',
                $saisie,
                "L'enregistrement a échoué. Merci de réessayer."
            );
        }

        Flash::add('success', "L'agence {$nom} a été créée.");
        $this->redirect('/admin/agences');
    }

    /**
     * Formulaire de modification, pré-rempli.
     *
     * @param int $id Identifiant de l'agence (issu de l'URL).
     *
     * @return string
     */
    public function edit(int $id): string
    {
        $agence = $this->agenceExistante($id);

        return $this->renderForm('Modifier une agence', "/admin/agences/{$id}/modifier", (string) $agence['nom'], null);
    }

    /**
     * Traite le formulaire de modification.
     *
     * @param int $id Identifiant de l'agence (issu de l'URL).
     *
     * @return string Le formulaire avec son erreur, en cas d'échec.
     */
    public function update(int $id): string
    {
        $this->agenceExistante($id);

        $saisie = $this->input('nom');
        $action = "/admin/agences/{$id}/modifier";
        $agenceModel = new AgenceModel(Database::getConnection());
        $nom = $this->valider($saisie, $agenceModel, $id, $erreur);

        if ($nom === null) {
            return $this->renderForm('Modifier une agence', $action, $saisie, $erreur);
        }

        try {
            $agenceModel->update($id, $nom);
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return $this->renderForm('Modifier une agence', $action, $saisie, "L'enregistrement a échoué. Merci de réessayer.");
        }

        Flash::add('success', "L'agence {$nom} a été modifiée.");
        $this->redirect('/admin/agences');
    }

    /**
     * Supprime une agence, si aucun trajet ne l'utilise.
     *
     * @param int $id Identifiant de l'agence (issu de l'URL).
     *
     * @return never
     */
    public function delete(int $id): never
    {
        $agence = $this->agenceExistante($id);
        $agenceModel = new AgenceModel(Database::getConnection());
        $nbTrajets = $agenceModel->countTrajets($id);

        // 1. Contrôle applicatif : message clair pour l'administrateur
        if ($nbTrajets > 0) {
            Flash::add(
                'danger',
                "Impossible de supprimer l'agence {$agence['nom']} : elle est utilisée par {$nbTrajets} trajet(s)."
            );
            $this->redirect('/admin/agences');
        }

        // 2. Filet de sécurité : la clé étrangère (ON DELETE RESTRICT) bloque
        //    aussi la suppression si un trajet a été créé entre-temps.
        try {
            $agenceModel->delete($id);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            Flash::add('danger', "Impossible de supprimer l'agence {$agence['nom']} : elle est utilisée par des trajets.");
            $this->redirect('/admin/agences');
        }

        Flash::add('success', "L'agence {$agence['nom']} a été supprimée.");
        $this->redirect('/admin/agences');
    }

    /**
     * Charge une agence existante, ou redirige vers la liste avec un message.
     *
     * @param int $id Identifiant de l'agence.
     *
     * @return array<string, mixed>
     */
    private function agenceExistante(int $id): array
    {
        $agence = (new AgenceModel(Database::getConnection()))->findById($id);

        if ($agence === null) {
            Flash::add('danger', "Cette agence n'existe pas.");
            $this->redirect('/admin/agences');
        }

        return $agence;
    }

    /**
     * Valide le nom saisi : format (AgenceValidator) puis unicité (base de données).
     *
     * @param string      $saisie      Nom saisi.
     * @param AgenceModel $agenceModel Modèle des agences.
     * @param int|null    $exceptId    Agence en cours de modification, ou null en création.
     * @param string|null $erreur      Reçoit le message d'erreur (paramètre par référence).
     *
     * @return string|null Le nom normalisé, ou null s'il est invalide.
     */
    private function valider(string $saisie, AgenceModel $agenceModel, ?int $exceptId, ?string &$erreur): ?string
    {
        $validator = new AgenceValidator();
        $nom = $validator->validate($saisie);

        if ($nom === null) {
            $erreur = $validator->error();

            return null;
        }

        if ($agenceModel->nomExiste($nom, $exceptId)) {
            $erreur = "Une agence porte déjà le nom {$nom}.";

            return null;
        }

        $erreur = null;

        return $nom;
    }

    /**
     * Affiche le formulaire d'agence (création ou modification).
     *
     * @param string      $titre  Titre de la page.
     * @param string      $action URL de soumission.
     * @param string      $nom    Valeur du champ.
     * @param string|null $erreur Message d'erreur, ou null.
     *
     * @return string
     */
    private function renderForm(string $titre, string $action, string $nom, ?string $erreur): string
    {
        return $this->render('admin/agences/form', [
            'titre'  => $titre,
            'action' => $action,
            'nom'    => $nom,
            'erreur' => $erreur,
        ]);
    }
}