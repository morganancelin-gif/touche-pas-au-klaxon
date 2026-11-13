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
     * du départ le plus proche au plus lointain, avec les coordonnées de leur auteur.
     *
     * @return list<array<string, mixed>>
     */
    public function findDisponibles(): array
    {
        $sql = 'SELECT t.id_trajet, t.date_heure_depart, t.date_heure_arrivee,
                       t.nb_places_total, t.nb_places_disponibles, t.id_employe,
                       ad.nom AS agence_depart, aa.nom AS agence_arrivee,
                       e.nom AS auteur_nom, e.prenom AS auteur_prenom,
                       e.telephone AS auteur_telephone, e.email AS auteur_email
                FROM trajet t
                JOIN agence ad ON ad.id_agence = t.id_agence_depart
                JOIN agence aa ON aa.id_agence = t.id_agence_arrivee
                JOIN employe e ON e.id_employe = t.id_employe
                WHERE t.date_heure_depart > NOW()
                  AND t.nb_places_disponibles > 0
                ORDER BY t.date_heure_depart ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Recherche un trajet par son identifiant.
     *
     * @param int $id Identifiant du trajet.
     *
     * @return array<string, mixed>|null Le trajet, ou null s'il n'existe pas.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM trajet WHERE id_trajet = :id');
        $stmt->execute(['id' => $id]);
        $trajet = $stmt->fetch();

        return $trajet === false ? null : $trajet;
    }

    /**
     * Supprime un trajet.
     *
     * @param int $id Identifiant du trajet.
     *
     * @return bool true si une ligne a été supprimée.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM trajet WHERE id_trajet = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() === 1;
    }
}