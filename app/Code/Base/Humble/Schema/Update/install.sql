/*
SQLyog Enterprise v12.4.3 (64 bit)
MySQL - 5.5.15 : Database - humble
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `core_categories` */

insert  into `core_categories`(`id`,`text`) values 
(1,'Logical Model'),
(2,'Entity'),
(3,'Utility'),
(4,'Component'),
(5,'Other');

/*Data for the table `core_packages` */

insert  into `core_packages`(`id`,`text`) values 
(1,'Core'),
(2,'Workflow Editor'),
(3,'Framework');

/*Data for the table `core_templaters` */

insert  into `core_templaters`(`id`,`templater`,`description`) values 
(1,'Smarty2','Smarty 2'),
(2,'Smarty3','Smarty 3'),
(3,'Twig','Twig'),
(4,'Rain2','Rain 2'),
(5,'Rain3','Rain 3'),
(6,'TBS','Tiny But Strong'),
(7,'PHP','PHP'),
(8,'PHPTal','PHP TAL');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
