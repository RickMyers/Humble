/*
SQLyog Enterprise v12.5.1 (64 bit)
MySQL - 5.5.15 : Database - humble
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `humble_templaters` */

insert  into `humble_templaters`(`id`,`templater`,`extension`,`description`) values 
(2,'Smarty3','tpl','Smarty 3'),
(3,'Twig','twig','Twig'),
(5,'Rain3','rain','Rain 3'),
(6,'TBS','tbs','Tiny But Strong'),
(7,'PHP','php','PHP'),
(8,'PHPTal','phptal','PHP TAL'),
(10,'Mustache','mustache','Mustache'),
(11,'Latte','latte','Latte');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
