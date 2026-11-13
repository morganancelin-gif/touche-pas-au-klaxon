-- ==========================================================
-- Touche pas au klaxon - Script de création de la base
-- Schéma physique issu du MLD (docs/mld.txt)
-- SGBD cible : MariaDB 10.4+ / MySQL 8+
-- ATTENTION : supprime la base existante (usage développement)
-- ==========================================================

DROP DATABASE IF EXISTS touche_pas_au_klaxon;

CREATE DATABASE touche_pas_au_klaxon
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE touche_pas_au_klaxon;

-- ----------------------------------------------------------
-- Table employe : données issues du SI RH
-- (pas de création/modification/suppression dans l'appli)
-- ----------------------------------------------------------
CREATE TABLE employe (
    id_employe   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom          VARCHAR(100) NOT NULL,
    prenom       VARCHAR(100) NOT NULL,
    telephone    VARCHAR(20)  NOT NULL,
    email        VARCHAR(255) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL COMMENT 'Hash password_hash()',
    role         ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    CONSTRAINT pk_employe PRIMARY KEY (id_employe),
    CONSTRAINT uq_employe_email UNIQUE (email)              -- RG4
) ENGINE = InnoDB;

-- ----------------------------------------------------------
-- Table agence : villes d'implantation (gérées par l'admin)
-- ----------------------------------------------------------
CREATE TABLE agence (
    id_agence INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom       VARCHAR(100) NOT NULL,
    CONSTRAINT pk_agence PRIMARY KEY (id_agence),
    CONSTRAINT uq_agence_nom UNIQUE (nom)                   -- RG4
) ENGINE = InnoDB;

-- ----------------------------------------------------------
-- Table trajet : trajets proposés au covoiturage
-- id_employe = auteur du trajet et personne à contacter
-- ----------------------------------------------------------
CREATE TABLE trajet (
    id_trajet             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    date_heure_depart     DATETIME         NOT NULL,
    date_heure_arrivee    DATETIME         NOT NULL,
    nb_places_total       TINYINT UNSIGNED NOT NULL,
    nb_places_disponibles TINYINT UNSIGNED NOT NULL,
    id_employe            INT UNSIGNED     NOT NULL,
    id_agence_depart      INT UNSIGNED     NOT NULL,
    id_agence_arrivee     INT UNSIGNED     NOT NULL,
    CONSTRAINT pk_trajet PRIMARY KEY (id_trajet),
    CONSTRAINT fk_trajet_employe FOREIGN KEY (id_employe)
        REFERENCES employe (id_employe)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT fk_trajet_agence_depart FOREIGN KEY (id_agence_depart)
        REFERENCES agence (id_agence)
        ON DELETE RESTRICT ON UPDATE RESTRICT,              -- RG5
    CONSTRAINT fk_trajet_agence_arrivee FOREIGN KEY (id_agence_arrivee)
        REFERENCES agence (id_agence)
        ON DELETE RESTRICT ON UPDATE RESTRICT,              -- RG5
    CONSTRAINT ck_trajet_agences
        CHECK (id_agence_depart <> id_agence_arrivee),      -- RG1
    CONSTRAINT ck_trajet_dates
        CHECK (date_heure_arrivee > date_heure_depart),     -- RG2
    CONSTRAINT ck_trajet_places
        CHECK (nb_places_total >= 1
               AND nb_places_disponibles <= nb_places_total) -- RG3
) ENGINE = InnoDB;

-- Index pour la page d'accueil (filtre et tri sur la date de départ)
CREATE INDEX idx_trajet_depart ON trajet (date_heure_depart);