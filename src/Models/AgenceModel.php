<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * Accès aux données de la table agence.
 */
class AgenceModel
{
    /**
     * @param PDO $pdo Connexion à la base de données.
     */
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Liste toutes les agences, par ordre alphabétique.
     *
     * @return list<array<string, mixed>>
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT id_agence, nom FROM agence ORDER BY nom ASC');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Liste toutes les agences avec le nombre de trajets qui les utilisent.
     *
     * @return list<array<string, mixed>>
     */
    public function findAllAvecNbTrajets(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.id_agence, a.nom,
                    (SELECT COUNT(*) FROM trajet t
                     WHERE t.id_agence_depart = a.id_agence
                        OR t.id_agence_arrivee = a.id_agence) AS nb_trajets
             FROM agence a
             ORDER BY a.nom ASC'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Recherche une agence par son identifiant.
     *
     * @param int $id Identifiant de l'agence.
     *
     * @return array<string, mixed>|null L'agence, ou null si elle n'existe pas.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id_agence, nom FROM agence WHERE id_agence = :id');
        $stmt->execute(['id' => $id]);
        $agence = $stmt->fetch();

        return $agence === false ? null : $agence;
    }

    /**
     * Indique si une agence porte déjà ce nom.
     *
     * La comparaison suit la collation de la base (utf8mb4_unicode_ci) :
     * elle ignore les majuscules et les accents ("paris" = "Paris").
     *
     * @param string   $nom      Nom à vérifier.
     * @param int|null $exceptId Agence à ignorer (celle qu'on modifie), ou null.
     *
     * @return bool
     */
    public function nomExiste(string $nom, ?int $exceptId = null): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM agence WHERE nom = :nom AND id_agence <> :except'
        );
        $stmt->execute(['nom' => $nom, 'except' => $exceptId ?? 0]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Compte les trajets qui partent de cette agence ou qui y arrivent.
     *
     * @param int $id Identifiant de l'agence.
     *
     * @return int
     */
    public function countTrajets(int $id): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM trajet WHERE id_agence_depart = :depart OR id_agence_arrivee = :arrivee'
        );
        $stmt->execute(['depart' => $id, 'arrivee' => $id]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Crée une agence.
     *
     * @param string $nom Nom de la ville.
     *
     * @return int Identifiant de l'agence créée.
     */
    public function create(string $nom): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO agence (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $nom]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Renomme une agence.
     *
     * @param int    $id  Identifiant de l'agence.
     * @param string $nom Nouveau nom.
     *
     * @return void
     */
    public function update(int $id, string $nom): void
    {
        $stmt = $this->pdo->prepare('UPDATE agence SET nom = :nom WHERE id_agence = :id');
        $stmt->execute(['nom' => $nom, 'id' => $id]);
    }

    /**
     * Supprime une agence.
     *
     * La base refuse la suppression d'une agence utilisée par un trajet
     * (clé étrangère ON DELETE RESTRICT) : une PDOException est alors levée.
     *
     * @param int $id Identifiant de l'agence.
     *
     * @return bool true si une ligne a été supprimée.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM agence WHERE id_agence = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() === 1;
    }
}