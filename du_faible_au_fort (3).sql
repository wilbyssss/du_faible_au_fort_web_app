-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 19 avr. 2025 à 20:24
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `du_faible_au_fort`
--

-- --------------------------------------------------------

--
-- Structure de la table `administration`
--

CREATE TABLE `administration` (
  `id_admin` int(11) NOT NULL,
  `username_admin` varchar(30) DEFAULT NULL,
  `email_admin` varchar(100) DEFAULT NULL,
  `password_admin` varchar(100) DEFAULT NULL,
  `role_admin` int(11) DEFAULT NULL,
  `statut_admin` int(11) NOT NULL DEFAULT 0,
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administration`
--

INSERT INTO `administration` (`id_admin`, `username_admin`, `email_admin`, `password_admin`, `role_admin`, `statut_admin`, `remember_token`) VALUES
(1, 'Admin', 'admin@gmail.com', 'administrators', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `avoir`
--

CREATE TABLE `avoir` (
  `id_avoir` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avoir`
--

INSERT INTO `avoir` (`id_avoir`, `id_classe`, `id_niveau`) VALUES
(1, 2, 1),
(2, 1, 2),
(3, 1, 1),
(4, 3, 3),
(7, 5, 2);

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id_classe` int(11) NOT NULL,
  `nom_classe` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id_classe`, `nom_classe`) VALUES
(1, 'SIL'),
(2, 'CP'),
(4, 'CE1');

-- --------------------------------------------------------

--
-- Structure de la table `exercice_a_trou`
--

CREATE TABLE `exercice_a_trou` (
  `id_ex_trou` int(11) NOT NULL,
  `id_theme` int(11) DEFAULT NULL,
  `id_text_training` int(11) DEFAULT NULL,
  `libelle_ex` varchar(100) DEFAULT NULL,
  `instruction_globale` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `exercice_a_trou`
--

INSERT INTO `exercice_a_trou` (`id_ex_trou`, `id_theme`, `id_text_training`, `libelle_ex`, `instruction_globale`) VALUES
(1, 1, 1, 'Compléter les phrases', 'Complétez les blancs avec les mots appropriés.Complétez les blancs avec les mots appropriés.');

-- --------------------------------------------------------

--
-- Structure de la table `niveau_difficulte`
--

CREATE TABLE `niveau_difficulte` (
  `id_niveau` int(11) NOT NULL,
  `nom_niveau` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `niveau_difficulte`
--

INSERT INTO `niveau_difficulte` (`id_niveau`, `nom_niveau`) VALUES
(1, 'Facile'),
(2, 'Difficiles');

-- --------------------------------------------------------

--
-- Structure de la table `phrase_a_trou`
--

CREATE TABLE `phrase_a_trou` (
  `id_phrase_a_trou` int(11) NOT NULL,
  `id_ex_trou` int(11) NOT NULL,
  `libelle_phrase_a_trou` varchar(500) DEFAULT NULL,
  `indication_phr` varchar(500) DEFAULT NULL,
  `reponse_correspondante` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `phrase_a_trou`
--

INSERT INTO `phrase_a_trou` (`id_phrase_a_trou`, `id_ex_trou`, `libelle_phrase_a_trou`, `indication_phr`, `reponse_correspondante`) VALUES
(1, 1, 'Je suis_au marché', 'Aller', 'Allé'),
(2, 1, 'Elle a _ mon gâteau', 'manger', 'mangée'),
(3, 1, 'Elles sont _ sans rien dire.', 'Sortir', 'sorties'),
(5, 5, 'Le chat _  sur le tapis.', 'Dormir', 'dort'),
(6, 6, 'La souris _ quand le chat n\'est pas là', 'Danser', 'Danse');

-- --------------------------------------------------------

--
-- Structure de la table `retrouver`
--

CREATE TABLE `retrouver` (
  `id_retrouver` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `session_app_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `retrouver`
--

INSERT INTO `retrouver` (`id_retrouver`, `session_id`, `session_app_id`) VALUES
(1, 1, 1),
(2, 2, 25),
(3, 3, 28),
(4, 4, 28),
(5, 5, 28),
(6, 6, 33),
(7, 7, 33),
(8, 8, 41),
(9, 9, 43),
(10, 10, 46),
(11, 11, 58),
(12, 12, 58),
(13, 13, 58),
(14, 14, 58),
(15, 15, 62),
(16, 16, 62),
(17, 17, 62),
(18, 18, 63),
(19, 19, 63);

-- --------------------------------------------------------

--
-- Structure de la table `roles_admin`
--

CREATE TABLE `roles_admin` (
  `id_role` int(11) NOT NULL,
  `nom_role` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles_admin`
--

INSERT INTO `roles_admin` (`id_role`, `nom_role`) VALUES
(1, 'administrateur'),
(2, 'chargeur de contenu');

-- --------------------------------------------------------

--
-- Structure de la table `session_app`
--

CREATE TABLE `session_app` (
  `session_app_id` int(11) NOT NULL,
  `date_con` datetime NOT NULL,
  `date_dec` datetime NOT NULL,
  `device_info` varchar(100) NOT NULL,
  `app_version` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `session_app`
--

INSERT INTO `session_app` (`session_app_id`, `date_con`, `date_dec`, `device_info`, `app_version`, `user_id`) VALUES
(1, '2025-04-10 04:42:32', '2025-04-10 06:32:15', 'vivo V2309A', '1.0.0 (1)', 2),
(2, '2025-04-10 04:43:55', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(3, '2025-04-10 04:54:56', '2025-04-10 04:57:14', 'vivo V2309A', '1.0.0 (1)', 2),
(4, '2025-04-10 04:59:57', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(5, '2025-04-10 05:14:26', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(6, '2025-04-10 05:22:17', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(7, '2025-04-10 05:26:03', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(8, '2025-04-10 05:28:32', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(9, '2025-04-10 05:28:44', '2025-04-10 05:29:10', 'vivo V2309A', '1.0.0 (1)', 2),
(10, '2025-04-10 05:29:29', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(11, '2025-04-10 05:33:28', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(12, '2025-04-10 06:38:15', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(13, '2025-04-10 06:53:50', '2025-04-10 06:58:27', 'vivo V2309A', '1.0.0 (1)', 2),
(14, '2025-04-10 06:58:52', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(15, '2025-04-10 07:59:57', '2025-04-10 08:00:38', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 10),
(16, '2025-04-10 08:00:54', '2025-04-10 08:01:04', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 2),
(17, '2025-04-10 08:01:26', '0000-00-00 00:00:00', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 2),
(18, '2025-04-10 08:12:16', '0000-00-00 00:00:00', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 10),
(19, '2025-04-10 08:18:08', '0000-00-00 00:00:00', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 10),
(20, '2025-04-10 08:20:40', '0000-00-00 00:00:00', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 10),
(21, '2025-04-10 08:21:51', '0000-00-00 00:00:00', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 10),
(22, '2025-04-10 08:25:59', '0000-00-00 00:00:00', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 10),
(23, '2025-04-10 08:27:21', '0000-00-00 00:00:00', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 10),
(24, '2025-04-10 17:43:40', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(25, '2025-04-10 17:46:04', '2025-04-10 17:47:31', 'vivo V2309A', '1.0.0 (1)', 2),
(26, '2025-04-10 17:47:59', '2025-04-10 18:00:49', 'vivo V2309A', '1.0.0 (1)', 2),
(27, '2025-04-10 18:01:49', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(28, '2025-04-10 20:53:18', '2025-04-10 20:58:58', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 11),
(29, '2025-04-10 20:59:13', '2025-04-10 20:59:37', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 11),
(30, '2025-04-10 20:59:41', '2025-04-10 21:01:00', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 11),
(31, '2025-04-10 21:05:12', '2025-04-10 21:05:26', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 11),
(32, '2025-04-10 21:05:29', '2025-04-10 21:05:44', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 11),
(33, '2025-04-10 21:09:51', '2025-04-10 21:17:48', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 2),
(34, '2025-04-10 21:17:57', '2025-04-10 21:18:15', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 2),
(35, '2025-04-13 06:47:54', '2025-04-13 06:51:37', 'vivo V2309A', '1.0.0 (1)', 2),
(36, '2025-04-13 06:52:36', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(37, '2025-04-13 06:53:22', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(38, '2025-04-13 06:54:59', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(39, '2025-04-13 06:56:16', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(40, '2025-04-13 06:59:36', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(41, '2025-04-13 07:08:26', '2025-04-13 07:20:37', 'vivo V2309A', '1.0.0 (1)', 2),
(42, '2025-04-13 07:21:02', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(43, '2025-04-13 07:29:07', '2025-04-13 07:30:36', 'vivo V2309A', '1.0.0 (1)', 2),
(44, '2025-04-13 07:30:57', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(45, '2025-04-13 07:31:36', '2025-04-13 07:32:02', 'vivo V2309A', '1.0.0 (1)', 2),
(46, '2025-04-13 07:32:28', '2025-04-13 07:33:20', 'vivo V2309A', '1.0.0 (1)', 11),
(47, '2025-04-13 07:33:38', '2025-04-13 12:35:34', 'vivo V2309A', '1.0.0 (1)', 11),
(48, '2025-04-14 06:23:48', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(49, '2025-04-14 07:08:41', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(50, '2025-04-14 07:12:35', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(51, '2025-04-14 07:18:01', '2025-04-14 08:28:42', 'vivo V2309A', '1.0.0 (1)', 2),
(52, '2025-04-14 08:29:05', '0000-00-00 00:00:00', 'vivo V2309A', '1.0.0 (1)', 2),
(53, '2025-04-16 02:31:04', '2025-04-16 03:04:15', 'Xiaomi 2211133C', '1.0.0 (1)', 2),
(54, '2025-04-16 06:26:31', '2025-04-16 06:26:53', 'Xiaomi 2211133C', '1.0.0 (1)', 2),
(55, '2025-04-16 06:28:25', '2025-04-16 07:27:47', 'Xiaomi 2211133C', '1.0.0 (1)', 2),
(56, '2025-04-16 07:28:08', '0000-00-00 00:00:00', 'Xiaomi 2211133C', '1.0.0 (1)', 2),
(57, '2025-04-17 09:12:47', '2025-04-17 09:13:06', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 10),
(58, '2025-04-17 09:13:28', '2025-04-17 09:14:10', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 2),
(59, '2025-04-18 05:25:43', '2025-04-18 05:25:57', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 2),
(60, '2025-04-18 05:25:58', '2025-04-18 05:26:03', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 2),
(61, '2025-04-18 05:26:04', '2025-04-18 05:26:07', 'TECNO MOBILE LIMITED TECNO LD7', '1.0.0 (1)', 2),
(62, '2025-04-18 07:46:46', '0000-00-00 00:00:00', 'Xiaomi 2211133C', '1.0.0 (1)', 2),
(63, '2025-04-18 08:00:31', '2025-04-18 08:02:53', 'Xiaomi 2211133C', '1.0.0 (1)', 2),
(64, '2025-04-18 08:03:18', '0000-00-00 00:00:00', 'Xiaomi 2211133C', '1.0.0 (1)', 2);

-- --------------------------------------------------------

--
-- Structure de la table `session_exercice`
--

CREATE TABLE `session_exercice` (
  `session_id` int(11) NOT NULL,
  `id_utilisateurs` int(11) DEFAULT NULL,
  `date_connexion` datetime DEFAULT NULL,
  `date_deconnexion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `session_exercice`
--

INSERT INTO `session_exercice` (`session_id`, `id_utilisateurs`, `date_connexion`, `date_deconnexion`) VALUES
(1, 2, '2025-04-10 05:43:12', '2025-04-10 05:43:43'),
(2, 2, '2025-04-10 18:46:44', '2025-04-10 18:47:11'),
(3, 11, '2025-04-10 21:55:40', '2025-04-10 21:57:25'),
(4, 11, '2025-04-10 21:57:46', NULL),
(5, 11, '2025-04-10 21:58:22', '2025-04-10 21:58:48'),
(6, 2, '2025-04-10 22:15:08', NULL),
(7, 2, '2025-04-10 22:17:04', '2025-04-10 22:17:29'),
(8, 2, '2025-04-13 08:20:04', '2025-04-13 08:20:32'),
(9, 2, '2025-04-13 08:29:47', '2025-04-13 08:30:16'),
(10, 11, '2025-04-13 08:32:47', '2025-04-13 08:33:15'),
(11, 2, '2025-04-17 10:13:42', NULL),
(12, 2, '2025-04-17 10:13:48', NULL),
(13, 2, '2025-04-17 10:13:55', NULL),
(14, 2, '2025-04-17 10:14:05', NULL),
(15, 2, '2025-04-18 08:47:30', NULL),
(16, 2, '2025-04-18 08:49:46', NULL),
(17, 2, '2025-04-18 08:49:55', NULL),
(18, 2, '2025-04-18 09:00:50', NULL),
(19, 2, '2025-04-18 09:01:53', '2025-04-18 09:02:02');

-- --------------------------------------------------------

--
-- Structure de la table `text_training`
--

CREATE TABLE `text_training` (
  `id_text_training` int(11) NOT NULL,
  `titre_text` varchar(50) DEFAULT NULL,
  `contenu_text_training` varchar(1000) DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `text_training`
--

INSERT INTO `text_training` (`id_text_training`, `titre_text`, `contenu_text_training`, `visibility`) VALUES
(1, 'Le Mystère du Verbe Caché', 'Dans un petit village perdu au milieu des montagnes, vivait un vieux sage nommé Maître Grégoire. Chaque matin, il enseignait aux enfants l’art des mots et des conjugaisons. Un jour, il leur lança un défi :  \r\n\r\n> \"Si vous trouvez le verbe caché dans cette phrase et que vous le conjuguez correctement au passé simple, je vous révélerai le secret du livre enchanté.\"  \r\n\r\nLes enfants se mirent alors à lire attentivement :  \r\n*\"Chaque nuit, les étoiles brillaient dans le ciel, et le vent murmurait à ', 1),
(2, ' Les Fondamentaux de l\'Intelligence Artificielle', 'systèmes capables de réaliser des tâches nécessitant normalement l\'intelligence humaine, comme la compréhension du langage naturel, la reconnaissance d\'images, la prise de décisions, etc. L\'IA utilise des algorithmes et des modèles mathématiques pour traiter de grandes quantités de données et en extraire des informations utiles.\r\n\r\nLes principales branches de l\'IA comprennent l\'apprentissage supervisé, l\'apprentissage non supervisé, et le renforcement. Les réseaux neuronaux, qui sont inspirés du', 1);

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE `themes` (
  `id_theme` int(11) NOT NULL,
  `nom_theme` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `themes`
--

INSERT INTO `themes` (`id_theme`, `nom_theme`) VALUES
(1, 'é/ée/és/ées/'),
(5, 'ç/ce/se/'),
(6, 'qsrgfd');

-- --------------------------------------------------------

--
-- Structure de la table `traitementexercice`
--

CREATE TABLE `traitementexercice` (
  `traitementEx_id` int(11) NOT NULL,
  `id_ex_trou` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `nbre_reponse_juste` int(11) DEFAULT NULL,
  `nbre_reponse_fausse` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `traitementexercice`
--

INSERT INTO `traitementexercice` (`traitementEx_id`, `id_ex_trou`, `session_id`, `nbre_reponse_juste`, `nbre_reponse_fausse`) VALUES
(1, 1, 1, 2, 1),
(2, 1, 2, 3, 0),
(3, 1, 3, 2, 1),
(4, 1, 5, 3, 0),
(5, 1, 7, 3, 0),
(6, 1, 8, 2, 1),
(7, 1, 9, 3, 0),
(8, 1, 10, 3, 0),
(9, 5, 19, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `type_compte`
--

CREATE TABLE `type_compte` (
  `id_type` int(11) NOT NULL,
  `nom_type` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `type_compte`
--

INSERT INTO `type_compte` (`id_type`, `nom_type`) VALUES
(1, 'Eleve'),
(2, 'Etudiant');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateurs` int(11) NOT NULL,
  `id_type` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `nom_user` varchar(30) DEFAULT NULL,
  `prenom_user` varchar(30) DEFAULT NULL,
  `genre_user` varchar(20) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `email_user` varchar(100) NOT NULL,
  `password_user` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateurs`, `id_type`, `id_classe`, `nom_user`, `prenom_user`, `genre_user`, `date_naissance`, `email_user`, `password_user`) VALUES
(1, 1, 1, 'Tchassem ', 'Franck ', 'masculin ', '2004-10-23', 'francktchassem28@gmail.com', '$2a$10$Alf8oLnM0oyOKJyB1ZqNyOEHJTbiUa9D0vIfCBnwvRrJCQ4aXjYLu'),
(2, 1, 1, 'lenyx', 'dev', 'masculin ', '2007-03-04', 'lenyxdev@gmail.com', '$2a$10$k7taEA8djFJfKtYCh3nWFOpviQ3778yLgjiHsSAe2jSKx8Y3d3LrO'),
(3, 1, 2, 'Djuimgho', 'Madeleine ', 'feminin', '1971-03-11', 'djuimghomadeleine@gmail.com', '$2a$10$oEG5a/jeuGovGjyiyTPAkexj.P4ICnidzTDEn5jdBQF2YTbkNqqoW'),
(4, 1, 2, 'doe', 'john', 'masculin ', '2001-03-03', 'johndoe@gmail.com', '$2a$10$eVN/lBGW8MXTDYoZY.rT0Ou/lAHfuoTUGmHPuu7gef8yY8vjnIdii'),
(5, 1, 1, 'zogo', 'serge', 'masculin', '2006-03-17', 'zogo@gmail.com', '$2a$10$QBFOf.d7yVCPAIc4qzD3ZeNypCVScD2soB4K3nvQmdS5xzy68caYm'),
(6, 1, 1, 'zogo', 'serge', 'masculin ', '2006-03-17', 'mony@gmail.com', '$2a$10$wvFrSTH4d5u6geuVvmuWV.xxicp6pOCVbxBly4zrgAQF5fTFxTXGK'),
(7, 1, 1, 'alber', 'kouemoiu', 'masculin ', '2006-03-17', 'alber@gmail.com', '$2a$10$g4q7HjmWuplHwHmHPQonjen4FKoHa6I.Csk6o9VMiueux2MdXUovK'),
(8, 1, 1, 'alber', 'kouemoiu', 'masculin ', '2006-03-17', 'albert@gmail.com', '$2a$10$bTd1wsXs.EVf951kkl049.ASGUmHLc31CEJ4ES4gYF1Se.Uk9L4L6'),
(9, 1, 1, 'madeleine', 'leontine', 'feminin', '2006-03-17', 'leontine@gmail.com', '$2a$10$53Vj/17q8jIZs2uGJ6YZGOKsh/koXcxAU4btL1BsZ/5g20aRepQJS'),
(10, 2, 1, 'Franck ', 'Joël ', 'masculin ', '2004-10-23', 'franck@gmail.com', '$2a$10$mUvQsliYFzlD2ov53hxEDOR1Pu9FE60.x0/im8hNfyoMjaUk4Atr2'),
(11, 1, 1, 'admin', 'administrateurs ', 'masculin ', '2003-03-21', 'admin@gmail.com', '$2a$10$hbPxiymLLVE3sPJ/a/mGHeynlsgE9o8KKluXV/2dFOYT/O0W0FPYS');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administration`
--
ALTER TABLE `administration`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `fk_role` (`role_admin`);

--
-- Index pour la table `avoir`
--
ALTER TABLE `avoir`
  ADD PRIMARY KEY (`id_avoir`),
  ADD KEY `id_niveau` (`id_niveau`),
  ADD KEY `id_classe` (`id_classe`);

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id_classe`);

--
-- Index pour la table `exercice_a_trou`
--
ALTER TABLE `exercice_a_trou`
  ADD PRIMARY KEY (`id_ex_trou`),
  ADD KEY `id_theme` (`id_theme`),
  ADD KEY `id_text_training` (`id_text_training`);

--
-- Index pour la table `niveau_difficulte`
--
ALTER TABLE `niveau_difficulte`
  ADD PRIMARY KEY (`id_niveau`);

--
-- Index pour la table `phrase_a_trou`
--
ALTER TABLE `phrase_a_trou`
  ADD PRIMARY KEY (`id_phrase_a_trou`),
  ADD KEY `id_ex_trou` (`id_ex_trou`);

--
-- Index pour la table `retrouver`
--
ALTER TABLE `retrouver`
  ADD PRIMARY KEY (`id_retrouver`),
  ADD KEY `fk_retrouver_1` (`session_id`),
  ADD KEY `fk_retrouver_2` (`session_app_id`);

--
-- Index pour la table `roles_admin`
--
ALTER TABLE `roles_admin`
  ADD PRIMARY KEY (`id_role`);

--
-- Index pour la table `session_app`
--
ALTER TABLE `session_app`
  ADD PRIMARY KEY (`session_app_id`),
  ADD KEY `fk_session_app_user` (`user_id`);

--
-- Index pour la table `session_exercice`
--
ALTER TABLE `session_exercice`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `id_utilisateurs` (`id_utilisateurs`);

--
-- Index pour la table `text_training`
--
ALTER TABLE `text_training`
  ADD PRIMARY KEY (`id_text_training`);

--
-- Index pour la table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id_theme`);

--
-- Index pour la table `traitementexercice`
--
ALTER TABLE `traitementexercice`
  ADD PRIMARY KEY (`traitementEx_id`),
  ADD KEY `id_ex_trou` (`id_ex_trou`),
  ADD KEY `session_id` (`session_id`);

--
-- Index pour la table `type_compte`
--
ALTER TABLE `type_compte`
  ADD PRIMARY KEY (`id_type`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateurs`),
  ADD KEY `id_type` (`id_type`),
  ADD KEY `id_classe` (`id_classe`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administration`
--
ALTER TABLE `administration`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `avoir`
--
ALTER TABLE `avoir`
  MODIFY `id_avoir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id_classe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `exercice_a_trou`
--
ALTER TABLE `exercice_a_trou`
  MODIFY `id_ex_trou` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `niveau_difficulte`
--
ALTER TABLE `niveau_difficulte`
  MODIFY `id_niveau` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `phrase_a_trou`
--
ALTER TABLE `phrase_a_trou`
  MODIFY `id_phrase_a_trou` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `retrouver`
--
ALTER TABLE `retrouver`
  MODIFY `id_retrouver` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `roles_admin`
--
ALTER TABLE `roles_admin`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `session_app`
--
ALTER TABLE `session_app`
  MODIFY `session_app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT pour la table `session_exercice`
--
ALTER TABLE `session_exercice`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `text_training`
--
ALTER TABLE `text_training`
  MODIFY `id_text_training` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `themes`
--
ALTER TABLE `themes`
  MODIFY `id_theme` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `traitementexercice`
--
ALTER TABLE `traitementexercice`
  MODIFY `traitementEx_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `type_compte`
--
ALTER TABLE `type_compte`
  MODIFY `id_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateurs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `administration`
--
ALTER TABLE `administration`
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_admin`) REFERENCES `roles_admin` (`id_role`);

--
-- Contraintes pour la table `avoir`
--
ALTER TABLE `avoir`
  ADD CONSTRAINT `avoir_ibfk_1` FOREIGN KEY (`id_niveau`) REFERENCES `niveau_difficulte` (`id_niveau`),
  ADD CONSTRAINT `avoir_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`);

--
-- Contraintes pour la table `exercice_a_trou`
--
ALTER TABLE `exercice_a_trou`
  ADD CONSTRAINT `exercice_a_trou_ibfk_1` FOREIGN KEY (`id_theme`) REFERENCES `themes` (`id_theme`),
  ADD CONSTRAINT `exercice_a_trou_ibfk_2` FOREIGN KEY (`id_text_training`) REFERENCES `text_training` (`id_text_training`);

--
-- Contraintes pour la table `phrase_a_trou`
--
ALTER TABLE `phrase_a_trou`
  ADD CONSTRAINT `phrase_a_trou_ibfk_1` FOREIGN KEY (`id_ex_trou`) REFERENCES `exercice_a_trou` (`id_ex_trou`);

--
-- Contraintes pour la table `retrouver`
--
ALTER TABLE `retrouver`
  ADD CONSTRAINT `fk_retrouver_1` FOREIGN KEY (`session_id`) REFERENCES `session_exercice` (`session_id`),
  ADD CONSTRAINT `fk_retrouver_2` FOREIGN KEY (`session_app_id`) REFERENCES `session_app` (`session_app_id`);

--
-- Contraintes pour la table `session_app`
--
ALTER TABLE `session_app`
  ADD CONSTRAINT `fk_session_app_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_utilisateurs`);

--
-- Contraintes pour la table `session_exercice`
--
ALTER TABLE `session_exercice`
  ADD CONSTRAINT `session_exercice_ibfk_1` FOREIGN KEY (`id_utilisateurs`) REFERENCES `utilisateurs` (`id_utilisateurs`);

--
-- Contraintes pour la table `traitementexercice`
--
ALTER TABLE `traitementexercice`
  ADD CONSTRAINT `traitementexercice_ibfk_1` FOREIGN KEY (`id_ex_trou`) REFERENCES `exercice_a_trou` (`id_ex_trou`),
  ADD CONSTRAINT `traitementexercice_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `session_exercice` (`session_id`);

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_type`) REFERENCES `type_compte` (`id_type`),
  ADD CONSTRAINT `utilisateurs_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
