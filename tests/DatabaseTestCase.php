<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Database;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Classe de base des tests qui écrivent dans la base de données.
 *
 * Chaque test s'exécute dans une transaction annulée à la fin (rollBack) :
 * les tests peuvent créer, modifier et supprimer librement, la base
 * retrouve toujours son état initial. Chaque test crée aussi ses propres
 * données (agences, employé) : il ne dépend pas du jeu d'essai.
 */
abstract class DatabaseTestCase extends TestCase
{
    /**
     * Connexion utilisée par le test.
     *
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * Ouvre une transaction avant chaque test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->pdo = Database::getConnection();
        $this->pdo->beginTransaction();
    }

    /**
     * Annule toutes les écritures du test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    /**
     * Insère une agence de test et retourne son identifiant.
     *
     * @param string $nom Nom de l'agence.
     *
     * @return int
     */
    protected function creerAgence(string $nom): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO agence (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $nom]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Insère un employé de test et retourne son identifiant.
     *
     * @param string $email Adresse email (unique).
     *
     * @return int
     */
    protected function creerEmploye(string $email = 'test.unitaire@email.fr'): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO employe (nom, prenom, telephone, email, mot_de_passe, role)
             VALUES (:nom, :prenom, :telephone, :email, :mot_de_passe, :role)'
        );
        $stmt->execute([
            'nom'          => 'Testeur',
            'prenom'       => 'Unitaire',
            'telephone'    => '0600000000',
            'email'        => $email,
            'mot_de_passe' => password_hash('Test#2026', PASSWORD_DEFAULT),
            'role'         => 'user',
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Lit une ligne de la table trajet directement en SQL (pour vérifier un résultat).
     *
     * @param int $id Identifiant du trajet.
     *
     * @return array<string, mixed>|false
     */
    protected function lireTrajet(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM trajet WHERE id_trajet = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }
}
