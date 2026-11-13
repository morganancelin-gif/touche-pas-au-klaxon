<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use App\Models\EmployeModel;

/**
 * Connexion et déconnexion des employés.
 */
class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     *
     * @return string
     */
    public function loginForm(): string
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        return $this->render('auth/login', ['email' => '', 'error' => null]);
    }

    /**
     * Traite le formulaire de connexion.
     *
     * @return string Le formulaire avec un message d'erreur, en cas d'échec.
     */
    public function login(): string
    {
        $email = $this->input('email');
        $password = $this->input('password', false);

        // 1. Validation du format des données reçues
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || $password === '') {
            return $this->render('auth/login', [
                'email' => $email,
                'error' => 'Veuillez saisir une adresse email valide et votre mot de passe.',
            ]);
        }

        // 2. Vérification des identifiants
        $employe = (new EmployeModel(Database::getConnection()))->findByEmail($email);

        if ($employe === null || !password_verify($password, (string) $employe['mot_de_passe'])) {
            // Message volontairement vague : on ne révèle pas si l'email existe.
            return $this->render('auth/login', [
                'email' => $email,
                'error' => 'Identifiants incorrects.',
            ]);
        }

        // 3. Connexion : on ne garde en session que le strict nécessaire
        Auth::login([
            'id'     => (int) $employe['id_employe'],
            'nom'    => (string) $employe['nom'],
            'prenom' => (string) $employe['prenom'],
            'role'   => (string) $employe['role'],
        ]);

        Flash::add('success', 'Bonjour ' . $employe['prenom'] . ', vous êtes connecté.');
        $this->redirect('/');
    }

    /**
     * Déconnecte l'utilisateur.
     *
     * @return never
     */
    public function logout(): never
    {
        Auth::logout();
        Flash::add('success', 'Vous êtes déconnecté.');
        $this->redirect('/');
    }
}