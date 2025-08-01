CREATE
DATABASE IF NOT EXISTS garagephp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE
garagephp_db;

CREATE TABLE users
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE cars
(
    car_id         INT AUTO_INCREMENT PRIMARY KEY,
    marque          VARCHAR(100)   NOT NULL,
    modele          VARCHAR(100)   NOT NULL,
    annee YEAR NOT NULL,
    couleur         VARCHAR(50)    NOT NULL,
    immatriculation VARCHAR(20)    NOT NULL UNIQUE,
    prix            DECIMAL(10, 2) NOT NULL,
    statut          ENUM ('disponible','vendu') NOT NULL DEFAULT 'disponible',
    created_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
