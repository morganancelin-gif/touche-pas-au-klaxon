-- ==========================================================
-- Touche pas au klaxon - Jeu d'essai (alimentation initiale)
-- À exécuter APRÈS database/schema.sql
--
-- Comptes de test :
--   Admin       : alexandre.martin@email.fr / Admin#Klaxon2026
--   Utilisateur : sophie.dubois@email.fr    / User#Klaxon2026
--   (tous les autres employés : User#Klaxon2026)
-- ==========================================================

SET NAMES utf8mb4;
USE touche_pas_au_klaxon;

-- ----------------------------------------------------------
-- Agences (source : agences.txt)
-- ----------------------------------------------------------
INSERT INTO agence (id_agence, nom) VALUES
    ( 1, 'Paris'),
    ( 2, 'Lyon'),
    ( 3, 'Marseille'),
    ( 4, 'Toulouse'),
    ( 5, 'Nice'),
    ( 6, 'Nantes'),
    ( 7, 'Strasbourg'),
    ( 8, 'Montpellier'),
    ( 9, 'Bordeaux'),
    (10, 'Lille'),
    (11, 'Rennes'),
    (12, 'Reims');

-- ----------------------------------------------------------
-- Employés (source : users.txt, export du SI RH)
-- Mots de passe hashés avec password_hash() (bcrypt)
-- ----------------------------------------------------------
INSERT INTO employe (id_employe, nom, prenom, telephone, email, mot_de_passe, role) VALUES
    ( 1, 'Martin', 'Alexandre', '0612345678', 'alexandre.martin@email.fr',
         '$2y$10$791NveNZRF1PM5Aj3mmQiu/qROYUD69sed4JVSnhYsk84HFXb7iCO', 'admin'),
    ( 2, 'Dubois', 'Sophie', '0698765432', 'sophie.dubois@email.fr',
         '$2y$10$9nLXm0r.oeVGaz45C53zYuFhP9EUMWeWpCAxbmcM2CAX9bCvjLiFW', 'user'),
    ( 3, 'Bernard', 'Julien', '0622446688', 'julien.bernard@email.fr',
         '$2y$10$Qo6R8EsziuhD00Tkf1YAOOqNVTYKdFxhL4VOkb86vlaRiWb.1xzVW', 'user'),
    ( 4, 'Moreau', 'Camille', '0611223344', 'camille.moreau@email.fr',
         '$2y$10$/5dRKknILrcsjryXxaEeyuyWFpAMIaTfMuPoqSvUuMSbXj0XcgmUe', 'user'),
    ( 5, 'Lefèvre', 'Lucie', '0777889900', 'lucie.lefevre@email.fr',
         '$2y$10$pLGm7GmgXa8qxTo68xmAIuURzKQ0Mpts2bOBmXXOMw3LckBvbnUiK', 'user'),
    ( 6, 'Leroy', 'Thomas', '0655443322', 'thomas.leroy@email.fr',
         '$2y$10$53KPSjlqqGkCyJuA5hSJpugOD2CdXCg4LD5Kqd/bS1mq8jBcaQMQ2', 'user'),
    ( 7, 'Roux', 'Chloé', '0633221199', 'chloe.roux@email.fr',
         '$2y$10$ArGuKEZdpoWkEKqMIxIfpOaApZ19QuFWPOsGMWCJyv.SEPhvktkX6', 'user'),
    ( 8, 'Petit', 'Maxime', '0766778899', 'maxime.petit@email.fr',
         '$2y$10$RMg5x125oJ93sh7c4Cta6.QifBJgBqSGXR6rQREkQAj4DydARMPnG', 'user'),
    ( 9, 'Garnier', 'Laura', '0688776655', 'laura.garnier@email.fr',
         '$2y$10$k6L4AgT8vPDcz.7De/8sW.k6YOCrumxNqvCJBiIjIClmMdGRzZGmW', 'user'),
    (10, 'Dupuis', 'Antoine', '0744556677', 'antoine.dupuis@email.fr',
         '$2y$10$ClVwupugZG.DRyZv2ap.JeBba1HCb5cjBgr54sKufd19c9mZdW.BG', 'user'),
    (11, 'Lefebvre', 'Emma', '0699887766', 'emma.lefebvre@email.fr',
         '$2y$10$RCrcWZiAqPz5zSPL0A.abulYedSeXIXnWQAfaP.EnOxFaZqwo.V2u', 'user'),
    (12, 'Fontaine', 'Louis', '0655667788', 'louis.fontaine@email.fr',
         '$2y$10$COEU2IR/SoAwAFdOMlY1f.lfXbNoRNRGvwv4Halii5mZxuq7AmQoe', 'user'),
    (13, 'Chevalier', 'Clara', '0788990011', 'clara.chevalier@email.fr',
         '$2y$10$Bxr8SFnmxZzMrJ5lqyQuJOy6O/XTRAPDXt0AobPs.8vq4IOaIjeRm', 'user'),
    (14, 'Robin', 'Nicolas', '0644332211', 'nicolas.robin@email.fr',
         '$2y$10$64DfHv/dKTOKrGt9OFXRh.0M1OU45c/P1oUwVyPxCb/cg3UjG2hMq', 'user'),
    (15, 'Gauthier', 'Marine', '0677889922', 'marine.gauthier@email.fr',
         '$2y$10$/um.DIt4JfXdT9cNDMBI4OZw55d7Ows.9N2CD1odfdL/0PxgH5aUu', 'user'),
    (16, 'Fournier', 'Pierre', '0722334455', 'pierre.fournier@email.fr',
         '$2y$10$m4fuTltssz2SR/UrD9aAvuKzvLwEtyNCk11chDOd77jeqtnOi31yi', 'user'),
    (17, 'Girard', 'Sarah', '0688665544', 'sarah.girard@email.fr',
         '$2y$10$KRVcS/8PlVEwaMpx2O1N6OXp9/YH2iZ61K8JaNyTguGRt8RxWBYDO', 'user'),
    (18, 'Lambert', 'Hugo', '0611223366', 'hugo.lambert@email.fr',
         '$2y$10$2OfrO3XCHmZSF9Q8JgY5Zuzm9ZJf7UUC2fMsJkWZ3UQTZC/EPN5Iq', 'user'),
    (19, 'Masson', 'Julie', '0733445566', 'julie.masson@email.fr',
         '$2y$10$MJ.f6vlB73tfCYk52hTvr.Gx5MBtVNDamjHEFuIpHr5z/S6sYj7xy', 'user'),
    (20, 'Henry', 'Arthur', '0666554433', 'arthur.henry@email.fr',
         '$2y$10$ECIwVOVViNqu2mHvLa0WyOc6dqZg9TtgO2h1hnqD6/Wuzg1jQ0frq', 'user');

-- ----------------------------------------------------------
-- Trajets
-- Dates RELATIVES à la date d'exécution du script :
-- le jeu d'essai reste valide quel que soit le jour de la correction.
-- ----------------------------------------------------------
INSERT INTO trajet (id_trajet, date_heure_depart, date_heure_arrivee,
                    nb_places_total, nb_places_disponibles,
                    id_employe, id_agence_depart, id_agence_arrivee) VALUES
    -- Cas nominaux : doivent apparaître sur la page d'accueil
    ( 1, CURDATE() + INTERVAL '1 08:00' DAY_MINUTE,  CURDATE() + INTERVAL '1 12:30' DAY_MINUTE,  4, 3,  2,  1,  2), -- Paris -> Lyon (Sophie)
    ( 2, CURDATE() + INTERVAL '2 07:30' DAY_MINUTE,  CURDATE() + INTERVAL '2 11:00' DAY_MINUTE,  3, 2,  3,  2,  3), -- Lyon -> Marseille (Julien)
    ( 3, CURDATE() + INTERVAL '2 14:00' DAY_MINUTE,  CURDATE() + INTERVAL '2 17:00' DAY_MINUTE,  4, 1,  2,  1, 10), -- Paris -> Lille (Sophie)
    ( 4, CURDATE() + INTERVAL '3 09:00' DAY_MINUTE,  CURDATE() + INTERVAL '3 11:30' DAY_MINUTE,  5, 4,  4,  9,  4), -- Bordeaux -> Toulouse (Camille)
    ( 5, CURDATE() + INTERVAL '5 06:45' DAY_MINUTE,  CURDATE() + INTERVAL '5 08:15' DAY_MINUTE,  3, 3,  6,  6, 11), -- Nantes -> Rennes (Thomas)
    ( 6, CURDATE() + INTERVAL '7 08:00' DAY_MINUTE,  CURDATE() + INTERVAL '7 11:30' DAY_MINUTE,  4, 2,  8,  7, 12), -- Strasbourg -> Reims (Maxime)
    ( 7, CURDATE() + INTERVAL '10 10:00' DAY_MINUTE, CURDATE() + INTERVAL '10 14:30' DAY_MINUTE, 2, 1, 12,  8,  5), -- Montpellier -> Nice (Louis)
    -- Cas limites : ne doivent PAS apparaître sur la page d'accueil
    ( 8, CURDATE() + INTERVAL '4 08:00' DAY_MINUTE,  CURDATE() + INTERVAL '4 10:30' DAY_MINUTE,  4, 0,  5,  3,  5), -- Marseille -> Nice : complet (0 place)
    ( 9, CURDATE() - INTERVAL 2 DAY + INTERVAL 8 HOUR, CURDATE() - INTERVAL 2 DAY + INTERVAL 12 HOUR, 3, 2, 2, 2, 1); -- Lyon -> Paris : passé (J-2)

-- Résultat attendu sur la page d'accueil : trajets 1 à 7, dans cet ordre.
