-- Base de données : gestion_commandes
-- Création de la base de données

CREATE DATABASE IF NOT EXISTS gestion_commandes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gestion_commandes;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Table des clients
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code_client VARCHAR(20) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(20)
);

-- Table des produits
CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    montant_total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);

-- Insertion de l'utilisateur admin (mot de passe : admin2026)
INSERT INTO users (login, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertion de données de test pour les clients
INSERT INTO clients (code_client, nom, prenom, email, telephone) VALUES
('CLI001', 'Dupont', 'Jean', 'jean.dupont@email.com', '0601020304'),
('CLI002', 'Martin', 'Marie', 'marie.martin@email.com', '0602030405'),
('CLI003', 'Bernard', 'Pierre', 'pierre.bernard@email.com', '0603040506');

-- Insertion de données de test pour les produits
INSERT INTO produits (nom, description, prix, stock) VALUES
('Ordinateur Portable', 'PC portable 15.6 pouces', 899.99, 10),
('Souris sans fil', 'Souris ergonomique sans fil', 29.99, 50),
('Clavier mécanique', 'Clavier gaming RGB', 79.99, 25),
('Écran 24 pouces', 'Moniteur Full HD', 199.99, 15);
