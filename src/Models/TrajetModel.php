<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * Accès aux données de la table trajet.
 */
class TrajetModel
{
    /**
     * @param PDO $pdo Connexion à la base de données.
     */
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Liste les trajets à venir qui ont encore des places disponibles,
     * du départ le plus proche au plus lointain.
     *
     * @return list<array<string, mixed>>
     */
    public function findDisponibles(): array
    {
        $sql = 'SELECT t.id_trajet, t.date_heure_depart, t.date_heure_arrivee,
                       t.nb_places_total, t.nb_places_disponibles, t.id_employe,
                       ad.nom AS agence_depart, aa.nom AS agence_arrivee
                FROM trajet t
                JOIN agence ad ON ad.id_agence = t.id_agence_depart
                JOIN agence aa ON aa.id_agence = t.id_agence_arrivee
                WHERE t.date_heure_depart > NOW()
                  AND t.nb_places_disponibles > 0
                ORDER BY t.date_heure_depart ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}