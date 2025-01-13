-- Créer la base de données
CREATE DATABASE  melinox_zooarcadia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Utiliser la base de données
USE melinox_zooarcadia;

-- Table pour les utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(40),
    name VARCHAR(80),
);

-- Table pour les services
CREATE TABLE table_name (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT NOT NULL,
    name VARCHAR(24) NOT NULL
);

-- Table pour les aliments 
CREATE TABLE foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(40) NOT NULL,
    weight FLOAT NOT NULL,
    unit VARCHAR(4) NOT NULL
);

-- Table pour les habitats
CREATE TABLE habitats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    description TEXT,
);

-- Table pour les animaux
CREATE TABLE animals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_animal VARCHAR(40) NOT NULL,
    breed VARCHAR(80),
    description TEXT,
    id_habitats INT NOT NULL,
    state VARCHAR(255),
    views INT DEFAULT 0,
    FOREIGN KEY (id_habitats) REFERENCES habitats(id)
);

-- Table pour les rapports
CREATE TABLE reports_vet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_animals INT NOT NULL,
    id_foods INT NOT NULL,
    id_users INT NOT NULL,
    date DATETIME NOT NULL,
    comment TEXT NOT NULL,
    weight FLOAT NOT NULL,
    unit VARCHAR(10) NOT NULL,
    FOREIGN KEY (id_animals) REFERENCES animals(id),
    FOREIGN KEY (id_foods) REFERENCES foods(id),
    FOREIGN KEY (id_users) REFERENCES users(id)
);


...
