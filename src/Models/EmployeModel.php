<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * Accès aux données de la table employe.
 *
 * Les employés proviennent du SI RH : l'application ne fait que les lire.
 */
class EmployeModel
{
    /**
     * @param PDO $pdo Connexion à la base de données.
     */
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Recherche un employé par son adresse email (identifiant de connexion).
     *
     * @param string $email Adresse email.
     *
     * @return array<string, mixed>|null L'employé, ou null s'il n'existe pas.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_employe, nom, prenom, telephone, email, mot_de_passe, role
             FROM employe
             WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);
        $employe = $stmt->fetch();

        return $employe === false ? null : $employe;
    }

    /**
     * Recherche un employé par son identifiant (sans son mot de passe).
     *
     * @param int $id Identifiant de l'employé.
     *
     * @return array<string, mixed>|null L'employé, ou null s'il n'existe pas.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_employe, nom, prenom, telephone, email, role
             FROM employe
             WHERE id_employe = :id'
        );
        $stmt->execute(['id' => $id]);
        $employe = $stmt->fetch();

        return $employe === false ? null : $employe;
    }
}