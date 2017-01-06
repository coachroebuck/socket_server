# ************************************************************
# Sequel Pro SQL dump
# Version 4654
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.11)
# Database: lang2lang
# Generation Time: 2017-01-06 14:44:09 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table l2l_language
# ------------------------------------------------------------

DROP TABLE IF EXISTS `l2l_language`;

CREATE TABLE `l2l_language` (
  `language_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `language_name` text NOT NULL,
  `language_code` text NOT NULL,
  `native_language_name` text NOT NULL,
  `language_change_user_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `l2l_language` WRITE;
/*!40000 ALTER TABLE `l2l_language` DISABLE KEYS */;

INSERT INTO `l2l_language` (`language_id`, `language_name`, `language_code`, `native_language_name`, `language_change_user_id`, `date_created`, `last_modified_date`)
VALUES
	(1,'English','en','English',NULL,'2017-01-05 14:25:38',NULL),
	(2,'Spanish','es','Español',NULL,'2017-01-05 14:25:38',NULL),
	(3,'French','fr','Français',NULL,'2017-01-05 14:25:38',NULL),
	(4,'German','de','Deutsche',NULL,'2017-01-06 06:03:12',NULL);

/*!40000 ALTER TABLE `l2l_language` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table l2l_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `l2l_user`;

CREATE TABLE `l2l_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `email` text NOT NULL,
  `nickname` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
