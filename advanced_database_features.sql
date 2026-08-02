-- ==============================================================================
-- SAHELMARKET - OPTIMISATIONS ET FONCTIONNALITÉS BASE DE DONNÉES AVANCÉES
-- Adapté à la structure exacte des tables MySQL de SahelMarket
-- ==============================================================================

USE sahelmarket;

-- ------------------------------------------------------------------------------
-- 0. SÉCURISATION DE LA STRUCTURE DE LA TABLE ANNONCES
-- Ajout automatique de la colonne 'statut' si elle n'existe pas encore
-- ------------------------------------------------------------------------------
SET @dbname = DATABASE();
SET @tablename = "annonces";
SET @columnname = "statut";
SET @prepexecp = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND COLUMN_NAME = @columnname
    ) > 0,
    "SELECT 1;",
    "ALTER TABLE annonces ADD COLUMN statut VARCHAR(20) DEFAULT 'actif' AFTER date_publication;"
));
PREPARE alterIfNotExists FROM @prepexecp;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;


-- ------------------------------------------------------------------------------
-- 1. CRÉATION D'UNE VUE SQL (VIEW) : vue_annonces_completes
-- Permet de sélectionner facilement toutes les annonces actives avec les
-- coordonnées du vendeur et le nom de la catégorie (jointure simplifiée).
-- ------------------------------------------------------------------------------
DROP VIEW IF EXISTS vue_annonces_completes;

CREATE VIEW vue_annonces_completes AS
SELECT 
    a.id AS annonce_id,
    a.titre,
    a.description,
    a.prix,
    a.devise,
    a.image AS image_nom,
    a.date_publication,
    a.statut,
    u.id AS vendeur_id,
    u.nom AS vendeur_nom,
    u.email AS vendeur_email,
    u.telephone AS vendeur_telephone,
    c.id AS categorie_id,
    c.nom AS categorie_nom
FROM annonces a
INNER JOIN utilisateurs u ON a.utilisateur_id = u.id
INNER JOIN categories c ON a.categorie_id = c.id
WHERE a.statut = 'actif';


-- ------------------------------------------------------------------------------
-- 2. PROCÉDURE STOCKÉE (STORED PROCEDURE) : sp_ajouter_annonce
-- Procédure pour insérer une nouvelle annonce de manière sécurisée et atomique.
-- ------------------------------------------------------------------------------
DELIMITER //

DROP PROCEDURE IF EXISTS sp_ajouter_annonce //

CREATE PROCEDURE sp_ajouter_annonce(
    IN p_titre VARCHAR(150),
    IN p_description TEXT,
    IN p_prix DECIMAL(10, 2),
    IN p_devise VARCHAR(10),
    IN p_image VARCHAR(255),
    IN p_utilisateur_id INT,
    IN p_categorie_id INT
)
BEGIN
    INSERT INTO annonces (titre, description, prix, devise, image, utilisateur_id, categorie_id, date_publication, statut)
    VALUES (p_titre, p_description, p_prix, p_devise, p_image, p_utilisateur_id, p_categorie_id, NOW(), 'actif');
END //

DELIMITER ;


-- ------------------------------------------------------------------------------
-- 3. DÉCLENCHEUR (TRIGGER) : trg_apres_ajout_annonce
-- Modifie automatiquement le rôle de l'utilisateur de 'user' vers 'vendeur'
-- lorsqu'il publie sa première annonce.
-- ------------------------------------------------------------------------------
DELIMITER //

DROP TRIGGER IF EXISTS trg_apres_ajout_annonce //

CREATE TRIGGER trg_apres_ajout_annonce
AFTER INSERT ON annonces
FOR EACH ROW
BEGIN
    UPDATE utilisateurs 
    SET role = 'vendeur' 
    WHERE id = NEW.utilisateur_id AND role = 'user';
END //

DELIMITER ;