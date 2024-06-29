/*
SQLyog Community
MySQL - 8.0.23 : Database - humble
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `blog_whatsnew` */

CREATE TABLE `blog_whatsnew` (
  `id` int NOT NULL AUTO_INCREMENT,
  `version` char(10) DEFAULT NULL,
  `title` char(128) DEFAULT NULL,
  `article` text,
  `author` int DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `published` date DEFAULT NULL,
  `modified` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `blog_whatsnew_code` */

CREATE TABLE `blog_whatsnew_code` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int DEFAULT NULL,
  `language_id` int DEFAULT NULL,
  `lexicon_id` int DEFAULT NULL,
  `sample` char(255) DEFAULT NULL,
  `scroll` char(16) DEFAULT '100%',
  `height` char(16) DEFAULT '200px',
  `modified` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `blog_whatsnew_languages` */

CREATE TABLE `blog_whatsnew_languages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `language` char(32) DEFAULT NULL,
  `modified` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `blog_whatsnew_lexicons` */

CREATE TABLE `blog_whatsnew_lexicons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lexicon` char(255) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `modified` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

