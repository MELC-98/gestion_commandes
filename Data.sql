-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2026 at 11:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_commandes`
--
DROP DATABASE IF EXISTS `gestion_commandes`;
CREATE DATABASE IF NOT EXISTS `gestion_commandes` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gestion_commandes`;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--
-- Creation: Feb 27, 2026 at 10:40 AM
-- Last update: Feb 27, 2026 at 10:40 AM
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_client` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_client` (`code_client`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `clients`
--

TRUNCATE TABLE `clients`;
--
-- Dumping data for table `clients`
--

INSERT DELAYED IGNORE INTO `clients` (`id`, `code_client`, `prenom`, `nom`, `email`, `telephone`, `created_at`) VALUES
(1, 'CLI001', 'Mohamed', 'Talbi', 'talbi.mohamed@gmail.com', '0601020304', '2026-02-27 10:40:54'),
(2, 'CLI002', 'yassine', 'Moutaki', 'moutaki.yassine@outlook.com', '0602030405', '2026-02-27 10:40:54'),
(3, 'CLI003', 'Ahmed', 'Berada', 'berada.ahmed@hotmail.com', '0603040506', '2026-02-27 10:40:54'),
(4, 'CLI004','Hicham', 'Mtalssi', 'mtalssi.hicham@gmail.com', '0602255351', '2026-02-27');

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--
-- Creation: Feb 27, 2026 at 10:40 AM
-- Last update: Feb 27, 2026 at 10:44 AM
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `date_commande` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_client` (`client_id`),
  KEY `fk_produit` (`produit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `commandes`
--

TRUNCATE TABLE `commandes`;
-- --------------------------------------------------------

--
-- Table structure for table `produits`
--
-- Creation: Feb 27, 2026 at 10:40 AM
-- Last update: Feb 27, 2026 at 10:45 AM
--

DROP TABLE IF EXISTS `produits`;
CREATE TABLE IF NOT EXISTS `produits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `produits`
--

TRUNCATE TABLE `produits`;
--
-- Dumping data for table `produits`
--

INSERT DELAYED IGNORE INTO `produits` (`id`, `nom`, `description`, `prix`, `stock`, `created_at`) VALUES
(1, 'Ordinateur Portable Lenovo IdeaPad Slim 3 15IRH10 (83K100FDFE)', 'Intel® Core™ i5-13420H, 8C (4P + 4E) / 12T, P-core 2.1 / 4.6GHz, E-core 1.5 / 3.4GHz, 12MB\r\nRAM: 16 GB (8GB Soldered DDR5-4800 + 8GB SO-DIMM DDR5-4800)\r\nDisque dur: 512GB SSD M.2 2242 PCIe® 4.0x4 NVMe®\r\nÉcran: 15.3\" WUXGA (1920x1200) IPS 300nits Anti-glare, 45% NTSC, 60Hz\r\nCarte graphique: Intel® UHD Graphics\r\nWindows® 11 Home\r\nClavier: Non-backlit, français (AZERTY)\r\nPoids: À partir de 1.59 kg\r\nSacoche 16\" Laptop Topload T210 Noir (ECO) offerte', 7499.00, 15, '2026-02-27 10:45:34'),
(5, 'Ordinateur portable HP 15-fc0003nk (845B6EA)', 'AMD Ryzen™ 5 7520U (jusqu’à 4,3 GHz de fréquence Boost maximale, mémoire cache L3 4 Mo, 4 cœurs, 8 threads)\r\nRAM: 8 Go de mémoire RAM LPDDR5-5500 MHz (intégrée)\r\nDisque dur: SSD PCIe® NVMe™ M.2 512 Go\r\nÉcran: Full HD d’une diagonale de 39,6 cm (15,6 pouces) (1920 x 1080), micro-bords, antireflet, 250 nits, 45 % NTSC\r\nCarte graphique: AMD Radeon™\r\nWindows 11 Édition Familiale unilingue\r\nClavier: Complet gris clair rétroéclairé avec pavé numérique, Français (AZERTY)\r\nPoids: 1,59 kg', 6999.00, 18, '2026-02-27 10:45:34'),
(6, 'Ordinateur portable convertible Dell Latitude 9450 2-in1 (DL-LAT9450-2IN1)', 'Intel® Core Ultra U7-165U (12MB cache, 12 cores, 14 threads, up to 4.9 GHz Max Turbo)\r\nRAM: 16 GB LPDDR5x 7467 MT/s dual-channel\r\nDisque dur: 512 Go M.2 2230, TLC PCIe Gen 4 NVMe, SSD\r\nÉcran: Écran tactile 14\" QHD+ 2 560 x 1 600, antireflet, antisalissure, IPS, 500 cd/m²\r\nCarte graphique: Intel Integrated Graphics\r\nWindows 11 Professionnel\r\nClavier: Backlit, Français (AZERTY)\r\nPoids: À partir de 1,53 kg', 33799.00, 5, '2026-02-27 10:45:34'),
(7, 'Imprimante Multifonction Laser Couleur HP LaserJet Pro 3303sdw (499M6A)', 'Impression, copie, numérisation\r\nVitesse d\'impression noir: 25 pages par minute (norme ISO)\r\nVitesse d\'impression couleur: 25 pages par minute (norme ISO)\r\nQualité d\'impression noire: Jusqu\'à 600 x 600 ppp\r\nQualité d\'impression couleur: Jusqu\'à 600 x 600 ppp\r\nVolume de pages mensuel recommandé: 150 à 2 500\r\nImpression recto/verso: Automatique\r\nFonctionne avec: 4 (noire, cyan, magenta, jaune)\r\nConnectivité: Wi-Fi 802.11ac (double bande)\r\n1 port réseau Gigabit Ethernet 10/100/1000 Base-TX\r\n1 port USB 2.0 haut débit (appareil)\r\n1 port USB 2.0 haut débit (hôte)\r\nEthernet avec croisement automatique\r\nEncres fournies avec l\'imprimante: 4 toners HP LaserJet préinstallés', 5349.00, 7, '2026-02-27 10:45:34'),
(8, 'Imprimante Multifonction Laser Couleur Canon i-SENSYS MF655Cdw (5158C004AA)', 'Impression, copie et numérisation\r\nVitesse d\'impression noir: Recto : jusqu\'à 21 ppm (A4) Jusqu\'à 38 ppm (A5 - Paysage) Recto verso : jusqu\'à 12,7 ipm (A4)\r\nVitesse d\'impression couleur: Recto : jusqu\'à 21 ppm (A4) Jusqu\'à 38 ppm (A5 - Paysage) Recto verso : jusqu\'à 12,7 ipm (A4)\r\nQualité d\'impression noire: Jusqu\'à 1200 × 1200 ppp\r\nQualité d\'impression couleur: Jusqu\'à 1200 × 1200 ppp\r\nVolume de pages mensuel recommandé: 250 à 2500 pages par mois\r\nImpression recto/verso: Automatique\r\nFonctionne avec: 4 toners ( Noir, Cyan, Magenta, Jaune)\r\nConnectivité: USB 2.0 Haute Vitesse, 10BASE-T/100BASE-TX/1000Base-T, Wi-Fi, connexion directe sans fil\r\nEncres fournies avec l\'imprimante: 4 cartouches de toner de démarrage (1x noir et 1x cyan, magenta et jaune)', 4699.00, 3, '2026-02-27 10:45:34'),
(9, 'Ordinateur de bureau HP Pro 290 G9 Tour + Écran HP P22V G5 (A55A1ET)', 'Intel® Core™ i5-13500 (jusqu’à 4,8 GHz avec la technologie Intel® Turbo Boost, 24 Mo de mémoire cache L3, 14 cœurs, 20 threads)\r\nRAM: 8 Go de mémoire DDR4-3200 MT/s (1 x 8 Go)\r\nDisque dur: SSD PCIe® NVMe™ M.2 512 Go\r\nCarte graphique: Intel® UHD 770\r\nFreeDOS (Sans Windows)\r\nUnité centrale + Écran 21,45\" Full HD HP P22v G5', 8999.00, 23, '2026-02-27 10:45:34'),
(10, 'Ordinateur de bureau HP Pro 400 G9 + Écran HP S3 pro 21 (99Q94ET)', 'Intel® Core™ i5-14500 (jusqu’à 5,0 GHz avec la technologie Intel® Turbo Boost, 24 Mo de mémoire cache L3, 14 cœurs, 20 threads)\r\nRAM: 8 Go de mémoire DDR5-4800 MT/s (1 x 8 Go)\r\nDisque dur: SSD NVMe™ PCIe® 512 Go\r\nCarte graphique: Intel® UHD 770\r\nFreeDOS (Sans Windows)\r\nUnité centrale + Écran HP S3 pro 21', 11499.00, 12, '2026-02-27 10:45:34'),
(11, 'Clavier Bluetooth Logitech K250 Compact - Français (AZERTY) (920-013481)', 'Le clavier Bluetooth Logitech K250 Compact offre une frappe fluide, réactive et silencieuse dans un format ergonomique et complet. Grâce à sa connectivité Bluetooth® Low Energy, il se connecte instantanément à vos appareils sans nécessiter de dongle, libérant ainsi vos ports USB. Conçu pour durer, il résiste aux éclaboussures et offre jusqu’à 12 mois d’autonomie avec deux piles AAA incluses. Son design compact intègre un pavé numérique pour un gain de place sans compromis sur la productivité. Fabriqué avec 64 % de plastique recyclé, le K250 allie confort, fiabilité et durabilité pour un usage quotidien polyvalent sur Windows, macOS, iOS ou Android.', 200.00, 50, '2026-02-27 10:45:34'),
(12, 'Souris sans fil Logitech M171', 'Optez pour la simplicité avec la Logitech M171. Branchez le récepteur USB pour une connexion instantanée et profitez de la liberté sans fil jusqu\'à 10 mètres. Compacte et ambidextre, elle s\'adapte à toutes les mains et se glisse facilement dans votre sac. Son capteur optique offre un contrôle précis du curseur sur diverses surfaces, idéal pour les espaces de travail réduits. Compatible avec Windows, macOS, Chrome OS et Linux, la M171 est prête à l\'emploi.', 105.00, 50, '2026-02-27 10:45:34'),
(13, 'Switch Administrable HPE Aruba CX 6200F 24 ports Classe 4 PoE 4 ports SFP 370W (JL725B)', 'Le switch administrable HPE Aruba CX 6200F est conçu pour les succursales, campus et PME, offrant une connectivité rapide et fiable. Avec 24 ports PoE Classe 4 (jusqu’à 30 W/port) et 4 ports SFP+ 1/10G, il alimente vos périphériques tout en garantissant des performances élevées grâce à une capacité de commutation de 128 Gbit/s et un débit de 95,2 Mpps. Sa capacité d\'empilement (jusqu’à 8 membres) et sa gestion avancée font de ce switch une solution évolutive et performante pour vos besoins réseau.', 30599.00, 50, '2026-02-27 10:45:34'),
(14, 'Switch Administrable HPE Aruba CX 6200F 24 ports Classe 4 PoE 4 ports SFP 370W (JL725B)', 'Le switch administrable HPE Aruba CX 6200F est conçu pour les succursales, campus et PME, offrant une connectivité rapide et fiable. Avec 24 ports PoE Classe 4 (jusqu’à 30 W/port) et 4 ports SFP+ 1/10G, il alimente vos périphériques tout en garantissant des performances élevées grâce à une capacité de commutation de 128 Gbit/s et un débit de 95,2 Mpps. Sa capacité d\'empilement (jusqu’à 8 membres) et sa gestion avancée font de ce switch une solution évolutive et performante pour vos besoins réseau.', 30599.00, 50, '2026-02-27 10:45:34'),
(15, 'Switch Administrable HPE Aruba CX 6200F 24 ports Classe 4 PoE 4 ports SFP 370W (JL725B)', 'Le switch administrable HPE Aruba CX 6200F est conçu pour les succursales, campus et PME, offrant une connectivité rapide et fiable. Avec 24 ports PoE Classe 4 (jusqu’à 30 W/port) et 4 ports SFP+ 1/10G, il alimente vos périphériques tout en garantissant des performances élevées grâce à une capacité de commutation de 128 Gbit/s et un débit de 95,2 Mpps. Sa capacité d\'empilement (jusqu’à 8 membres) et sa gestion avancée font de ce switch une solution évolutive et performante pour vos besoins réseau.', 30599.00, 16, '2026-02-27 10:45:34'),
(16, 'Switch de bureau TP-Link TL-SG1210P 10 ports Gigabit avec 8 ports PoE+', 'Le switch de bureau TP-Link TL-SG1210P offre une solution pratique pour votre réseau. Avec 8 ports PoE+ qui transfèrent données et alimentation sur un seul câble, il simplifie votre infrastructure. Compatible avec les caméras IP, les téléphones IP et les EAP Omada, il s\'adapte à vos besoins. Son boîtier métal robuste, sans ventilateur, assure un fonctionnement silencieux et fiable. Avec des fonctions avancées telles que QoS et IGMP Snooping, il optimise votre trafic pour une expérience réseau fluide. Aucune configuration ni installation nécessaires, il est prêt à l\'emploi.', 1109.00, 13, '2026-02-27 10:45:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: Feb 27, 2026 at 10:40 AM
-- Last update: Feb 27, 2026 at 10:40 AM
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT DELAYED IGNORE INTO `users` (`id`, `login`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$wSHhWqY.87zcwbs2Nt0mJul9Z95l/cS4CjTaLN5T6Ecyun3ESwfaK', '2026-02-27 10:40:54');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `fk_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
