-- ==========================================================
-- Touche pas au klaxon - Utilisateur applicatif
-- Principe du moindre privilège : CRUD uniquement, sur cette base uniquement.
-- À exécuter une seule fois, en root.
-- Mot de passe de DÉVELOPPEMENT : à changer en production.
-- ==========================================================

CREATE USER IF NOT EXISTS 'klaxon_app'@'localhost' IDENTIFIED BY 'klaxon_dev_2026';
CREATE USER IF NOT EXISTS 'klaxon_app'@'127.0.0.1' IDENTIFIED BY 'klaxon_dev_2026';

GRANT SELECT, INSERT, UPDATE, DELETE
    ON touche_pas_au_klaxon.* TO 'klaxon_app'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE
    ON touche_pas_au_klaxon.* TO 'klaxon_app'@'127.0.0.1';