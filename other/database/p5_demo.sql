-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  lun. 04 fév. 2019 à 20:53
-- Version du serveur :  5.7.21
-- Version de PHP :  7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `oc_projet5_blog`
--

-- --------------------------------------------------------

--
-- Structure de la table `bl_category`
--

DROP TABLE IF EXISTS `bl_category`;
CREATE TABLE IF NOT EXISTS `bl_category` (
  `cat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bl_category`
--

INSERT INTO `bl_category` (`cat_id`, `cat_name`) VALUES
(21, 'A propos'),
(22, 'Blog'),
(23, 'Réalisations');

-- --------------------------------------------------------

--
-- Structure de la table `bl_category_tag`
--

DROP TABLE IF EXISTS `bl_category_tag`;
CREATE TABLE IF NOT EXISTS `bl_category_tag` (
  `ct_category_id_fk` int(10) UNSIGNED NOT NULL,
  `ct_tag_id_fk` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`ct_category_id_fk`,`ct_tag_id_fk`),
  KEY `fk_ct_tag_id_tag_id` (`ct_tag_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bl_category_tag`
--

INSERT INTO `bl_category_tag` (`ct_category_id_fk`, `ct_tag_id_fk`) VALUES
(21, 36),
(21, 37),
(22, 38),
(23, 38),
(22, 39),
(22, 42),
(22, 43);

-- --------------------------------------------------------

--
-- Structure de la table `bl_comment`
--

DROP TABLE IF EXISTS `bl_comment`;
CREATE TABLE IF NOT EXISTS `bl_comment` (
  `com_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `com_parent_id_fk` int(10) UNSIGNED DEFAULT NULL,
  `com_post_id_fk` int(10) UNSIGNED DEFAULT NULL,
  `com_author_id_fk` int(10) UNSIGNED DEFAULT NULL,
  `com_last_editor_id_fk` int(10) UNSIGNED DEFAULT NULL,
  `com_creation_date` datetime NOT NULL,
  `com_last_modification_date` datetime DEFAULT NULL,
  `com_content` text NOT NULL,
  PRIMARY KEY (`com_id`),
  KEY `fk_com_parent_id_com_id` (`com_parent_id_fk`),
  KEY `fk_com_author_id_m_id` (`com_author_id_fk`),
  KEY `fk_com_post_id_p_id` (`com_post_id_fk`),
  KEY `fk_com_last_editor_id_m_id` (`com_last_editor_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bl_member`
--

DROP TABLE IF EXISTS `bl_member`;
CREATE TABLE IF NOT EXISTS `bl_member` (
  `m_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `m_email` varchar(100) NOT NULL,
  `m_password` varchar(100) NOT NULL,
  `m_language` varchar(100) NOT NULL,
  `m_name` varchar(100) NOT NULL,
  `m_description` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bl_member`
--

INSERT INTO `bl_member` (`m_id`, `m_email`, `m_password`, `m_language`, `m_name`, `m_description`) VALUES
(58, 'zog.0@gmail.com', 'motdepasse0', 'Langue 0', 'Nom 0', 'Description 0'),
(59, 'zog.1@gmail.com', 'motdepasse1', 'Langue 1', 'Nom 1', 'Description 1'),
(61, 'zog.3@gmail.com', 'motdepasse3', 'Langue 3', 'Nom 3', 'Description 3'),
(62, 'zog.4@gmail.com', 'motdepasse4', 'Langue 4', 'Nom 4', 'Description 4');

-- --------------------------------------------------------

--
-- Structure de la table `bl_member_website`
--

DROP TABLE IF EXISTS `bl_member_website`;
CREATE TABLE IF NOT EXISTS `bl_member_website` (
  `mw_website_id_fk` int(10) UNSIGNED NOT NULL,
  `mw_member_id_fk` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`mw_website_id_fk`,`mw_member_id_fk`),
  KEY `fk_mw_member_id_m_id` (`mw_member_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bl_post`
--

DROP TABLE IF EXISTS `bl_post`;
CREATE TABLE IF NOT EXISTS `bl_post` (
  `p_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `p_author_id_fk` int(10) UNSIGNED DEFAULT NULL,
  `p_title` varchar(100) NOT NULL,
  `p_excerpt` varchar(300) NOT NULL,
  `p_content` text NOT NULL,
  `p_creation_date` datetime NOT NULL,
  `p_last_modification_date` datetime DEFAULT NULL,
  `p_last_editor_id_fk` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`p_id`),
  KEY `fk_p_author_id_m_id` (`p_author_id_fk`),
  KEY `fk_p_last_editor_id_m_id` (`p_last_editor_id_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bl_post`
--

INSERT INTO `bl_post` (`p_id`, `p_author_id_fk`, `p_title`, `p_excerpt`, `p_content`, `p_creation_date`, `p_last_modification_date`, `p_last_editor_id_fk`) VALUES
(21, 58, 'Petite présentation', 'Voici ma présentation', 'Je suis Nicolas Renvoisé et j\'adore coder !', '2019-02-04 16:51:47', NULL, NULL),
(22, 58, 'Mon CV', 'Voici mon CV, attention les yeux !', 'Avril 1987 : Naissance, c\'était pas de la tarte !', '2019-02-04 16:52:50', NULL, NULL),
(23, 58, 'Mémo CSS', 'Un petit mémo pour CSS', 'font-size: 1em; Permet de définir la taille du texte.', '2019-02-04 16:54:27', NULL, NULL),
(24, 58, 'Mémo PHP', 'Un mémo pour PHP.', 'die(); Contrairement à ce qu\'on pourrait croire, cette fonction ne tue pas le visiteur du site.', '2019-02-04 16:55:24', '2019-02-04 16:55:36', 59),
(25, 58, 'Courir pieds nus c\'est le pied !', 'Avant, j\'avais mal aux genoux. Mais ça, c\'était avant que je découvre la course pieds nus !', 'Et oui ! Fini les syndromes rotulien et autres blessures liées au port de chaussures amortissantes à la noix ! Courrez pieds nus !', '2019-02-04 20:49:19', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `bl_post_tag`
--

DROP TABLE IF EXISTS `bl_post_tag`;
CREATE TABLE IF NOT EXISTS `bl_post_tag` (
  `pt_post_id_fk` int(10) UNSIGNED NOT NULL,
  `pt_tag_id_fk` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`pt_post_id_fk`,`pt_tag_id_fk`),
  KEY `fk_pt_tag_tag_id` (`pt_tag_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bl_post_tag`
--

INSERT INTO `bl_post_tag` (`pt_post_id_fk`, `pt_tag_id_fk`) VALUES
(22, 36),
(21, 37),
(22, 37),
(23, 38),
(24, 38),
(23, 39),
(24, 39),
(23, 40),
(24, 41),
(25, 42),
(25, 43);

-- --------------------------------------------------------

--
-- Structure de la table `bl_role`
--

DROP TABLE IF EXISTS `bl_role`;
CREATE TABLE IF NOT EXISTS `bl_role` (
  `r_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `r_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`r_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bl_role`
--

INSERT INTO `bl_role` (`r_id`, `r_name`) VALUES
(1, 'Role 0'),
(3, 'Role 2'),
(4, 'Role 3'),
(5, 'Role 4');

-- --------------------------------------------------------

--
-- Structure de la table `bl_role_member`
--

DROP TABLE IF EXISTS `bl_role_member`;
CREATE TABLE IF NOT EXISTS `bl_role_member` (
  `rm_member_id_fk` int(10) UNSIGNED NOT NULL,
  `rm_role_id_fk` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`rm_member_id_fk`,`rm_role_id_fk`),
  KEY `fk_rm_role_id_r_id` (`rm_role_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bl_tag`
--

DROP TABLE IF EXISTS `bl_tag`;
CREATE TABLE IF NOT EXISTS `bl_tag` (
  `tag_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bl_tag`
--

INSERT INTO `bl_tag` (`tag_id`, `tag_name`) VALUES
(36, 'cv'),
(37, 'Nicolas Renvoisé'),
(38, 'Programmation'),
(39, 'Mémo'),
(40, 'css'),
(41, 'php'),
(42, 'Sport'),
(43, 'Santé');

-- --------------------------------------------------------

--
-- Structure de la table `bl_website`
--

DROP TABLE IF EXISTS `bl_website`;
CREATE TABLE IF NOT EXISTS `bl_website` (
  `web_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `web_url` varchar(100) DEFAULT NULL,
  `web_name` varchar(100) NOT NULL,
  `web_description` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`web_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `bl_website`
--

INSERT INTO `bl_website` (`web_id`, `web_url`, `web_name`, `web_description`) VALUES
(1, 'http://www.monsupersite-0.com', 'Site 0', 'Description 0'),
(3, 'http://www.monsupersite-2.com', 'Site 2', 'Description 2'),
(4, 'http://www.monsupersite-3.com', 'Site 3', 'Description 3'),
(5, 'http://www.monsupersite-4.com', 'Site 4', 'Description 4');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bl_category_tag`
--
ALTER TABLE `bl_category_tag`
  ADD CONSTRAINT `fk_ct_category_cat_id` FOREIGN KEY (`ct_category_id_fk`) REFERENCES `bl_category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ct_tag_id_tag_id` FOREIGN KEY (`ct_tag_id_fk`) REFERENCES `bl_tag` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `bl_comment`
--
ALTER TABLE `bl_comment`
  ADD CONSTRAINT `fk_com_author_id_m_id` FOREIGN KEY (`com_author_id_fk`) REFERENCES `bl_member` (`m_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_com_last_editor_id_m_id` FOREIGN KEY (`com_last_editor_id_fk`) REFERENCES `bl_member` (`m_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_com_parent_id_com_id` FOREIGN KEY (`com_parent_id_fk`) REFERENCES `bl_comment` (`com_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_com_post_id_p_id` FOREIGN KEY (`com_post_id_fk`) REFERENCES `bl_post` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `bl_member_website`
--
ALTER TABLE `bl_member_website`
  ADD CONSTRAINT `fk_mw_member_id_m_id` FOREIGN KEY (`mw_member_id_fk`) REFERENCES `bl_member` (`m_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mw_website_id_web_id` FOREIGN KEY (`mw_website_id_fk`) REFERENCES `bl_website` (`web_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `bl_post`
--
ALTER TABLE `bl_post`
  ADD CONSTRAINT `fk_p_author_id_m_id` FOREIGN KEY (`p_author_id_fk`) REFERENCES `bl_member` (`m_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_p_last_editor_id_m_id` FOREIGN KEY (`p_last_editor_id_fk`) REFERENCES `bl_member` (`m_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `bl_post_tag`
--
ALTER TABLE `bl_post_tag`
  ADD CONSTRAINT `fk_pt_post_id_p_id` FOREIGN KEY (`pt_post_id_fk`) REFERENCES `bl_post` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pt_tag_tag_id` FOREIGN KEY (`pt_tag_id_fk`) REFERENCES `bl_tag` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `bl_role_member`
--
ALTER TABLE `bl_role_member`
  ADD CONSTRAINT `fk_rm_member_id_m_id` FOREIGN KEY (`rm_member_id_fk`) REFERENCES `bl_member` (`m_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rm_role_id_r_id` FOREIGN KEY (`rm_role_id_fk`) REFERENCES `bl_role` (`r_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
