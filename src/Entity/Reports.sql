-- Insérer un nouveau rapport
INSERT INTO reports (idAnimals, idUsers, date, comment)
VALUES (13, 7, NOW(), 'Ceci est un commentaire de test');

-- Récupérer l'ID du rapport inséré
SET @report_id = LAST_INSERT_ID();

-- Insérer des aliments associés à ce rapport
INSERT INTO foods (name, weight, unit, report_id)
VALUES 
('Aliment 1', 2.5, 'kg', @report_id),
('Aliment 2', 1.0, 'kg', @report_id);

SELECT * FROM reports

SELECT * FROM users

SELECT * FROM foods

INSERT INTO reports (id_animals_id, id_users_id, date, comment, id_foods_id)
SELECT a.id, u.id, NOW(), 'Ceci est un commentaire de test', f.id
FROM animals a
JOIN users u ON u.id = 7
JOIN foods f ON f.id = 1
WHERE a.name_animal = 'Léo';

SHOW COLUMNS FROM reports;

USE melinox_zooarcadia;
SHOW TABLES;

ALTER TABLE reports ADD COLUMN weight DECIMAL(10, 2);