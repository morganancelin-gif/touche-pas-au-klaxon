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
}