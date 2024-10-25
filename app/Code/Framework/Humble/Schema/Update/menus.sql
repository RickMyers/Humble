/*
SQLyog Community
MySQL - 5.7.44 : Database - humble
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `admin_menus` */

DROP TABLE IF EXISTS `admin_menus`;

CREATE TABLE `admin_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu` char(48) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `function` char(255) DEFAULT NULL,
  `href` char(96) DEFAULT NULL,
  `target` char(32) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

/*Data for the table `admin_menus` */

insert  into `admin_menus`(`id`,`menu`,`parent_id`,`category_id`,`function`,`href`,`target`,`seq`,`modified`) values 
(1,'Administration',NULL,1,NULL,'/admin','_BLANK',1,'2023-12-22 16:58:07'),
(2,'Workflow Editor',NULL,1,NULL,'/paradigm/actions/open','_BLANK',2,'2023-12-22 17:00:15'),
(3,'Controller Designer',NULL,1,NULL,'/contrive/actions/open','_BLANK',3,'2023-12-22 17:00:42'),
(4,'API Tester',NULL,1,NULL,'/paragigm/api/home','_BLANK',4,'2023-12-22 17:01:10'),
(5,'Form Designer',NULL,1,NULL,'/paradigm/actions/designer','_BLANK',5,'2023-12-22 17:01:31'),
(6,'Logout',NULL,1,NULL,'/admin/user/logout',NULL,6,'2023-12-22 17:01:54'),
(7,'Generate',NULL,2,'Administration.documentation.run()',NULL,NULL,1,'2023-12-22 17:02:20'),
(8,'Developer Documentation',NULL,2,NULL,'/pages/TOC.htmls','_BLANK',2,'2023-12-22 17:03:06'),
(9,'API Documentation',NULL,2,'Administration.documentation.review()',NULL,NULL,3,'2023-12-22 17:04:02'),
(10,'Directory',NULL,3,'Administration.services.directory.index()',NULL,NULL,1,'2023-12-22 17:04:31'),
(11,'Authorizations',NULL,3,'Administration.services.directory.authorizations()',NULL,NULL,2,'2023-12-22 17:05:08'),
(12,'Create',NULL,4,NULL,NULL,NULL,1,'2023-12-22 17:23:19'),
(13,'Utilities',NULL,4,NULL,NULL,NULL,2,'2023-12-22 17:23:44'),
(14,'System',NULL,4,NULL,NULL,NULL,3,'2023-12-22 17:24:03'),
(15,'Secrets Manager',NULL,4,NULL,NULL,NULL,4,'2023-12-22 17:24:23'),
(16,'Workflows',NULL,4,NULL,NULL,NULL,5,'2023-12-22 17:24:35'),
(17,'New Package',12,NULL,'Administration.create.package()',NULL,NULL,1,'2023-12-22 17:26:09'),
(18,'New Module',12,NULL,'Administration.create.module()',NULL,NULL,2,'2023-12-22 17:26:29'),
(19,'New Component',12,NULL,'Administration.create.component()',NULL,NULL,3,'2023-12-22 17:26:37'),
(20,'New Controller',12,NULL,'Administration.create.controller()',NULL,NULL,4,'2023-12-22 17:27:08'),
(21,'Add New Category',13,NULL,'Administration.add.category()',NULL,NULL,1,'2023-12-22 17:28:14'),
(22,'Add New Package',13,NULL,'Administration.add.package()',NULL,NULL,2,'2023-12-22 17:28:51'),
(23,'Clone Templates',13,NULL,'Administration.templates.clone()',NULL,NULL,3,'2023-12-22 17:29:35'),
(24,'Upload File',13,NULL,'Administration.upload.form()',NULL,NULL,4,'2023-12-22 17:30:28'),
(25,'Cadence',NULL,5,NULL,NULL,NULL,1,'2023-12-22 17:30:54'),
(26,'SMTP',NULL,5,'',NULL,NULL,2,'2023-12-22 17:31:12'),
(27,'Monitor',NULL,5,'Administration.system.monitor.open()',NULL,NULL,3,'2023-12-22 17:31:33'),
(28,'Status',NULL,5,'Administration.system.status.open()',NULL,NULL,4,'2023-12-22 17:31:40'),
(29,'Start',25,NULL,'Administration.cadence.action(\"start\")',NULL,NULL,1,'2023-12-22 17:32:01'),
(30,'Stop',25,NULL,'Administration.cadence.action(\"stop\")',NULL,NULL,2,'2023-12-22 17:32:09'),
(31,'Reload',25,NULL,'Administration.cadence.action(\"reload\")',NULL,NULL,3,'2023-12-22 17:32:49'),
(32,'Clear PID',25,NULL,'Administration.cadence.action(\"clear\")',NULL,NULL,4,'2023-12-22 17:33:08'),
(33,'Tune...',25,NULL,'Administration.cadence.tune()',NULL,NULL,5,'2023-12-22 17:33:30'),
(36,'Add New Secret',15,NULL,'Administration.secrets.add()',NULL,NULL,1,'2023-12-22 17:37:58'),
(37,'Review/Update Secret',15,NULL,'Administration.secrets.review()',NULL,NULL,2,'2023-12-22 17:38:21'),
(38,'Add Export Target',16,NULL,'Administration.workflows.add.exportTarget()',NULL,NULL,1,'2023-12-22 17:38:56'),
(39,'Add Import Token',16,NULL,'Administration.workflows.add.importToken()',NULL,NULL,2,'2023-12-22 17:39:18'),
(40,'Query Logging',NULL,5,NULL,NULL,NULL,3,'2023-12-22 17:39:44'),
(41,'On',40,NULL,'window.event.stopPropagation()','/admin/log/queryon',NULL,1,'2023-12-22 17:40:04'),
(42,'Off',40,NULL,'window.event.stopPropagation()','/admin/log/queryoff',NULL,2,'2023-12-22 17:40:17'),
(43,'Maintenance Mode',NULL,5,NULL,NULL,NULL,3,'2023-12-22 17:41:07'),
(44,'Enter',43,NULL,'Administration.maintenance.enter()',NULL,NULL,1,'2023-12-22 17:41:40'),
(45,'Leave',43,NULL,'Administration.maintenance.leave()',NULL,NULL,2,'2023-12-22 17:42:30'),
(46,'Settings',26,NULL,'Administration.smtp.settings.open()',NULL,NULL,NULL,'2024-02-15 20:55:32'),
(47,'Tester',26,NULL,'Administration.smtp.settings.test()',NULL,NULL,NULL,'2024-02-15 20:55:53'),
(48,'Manage...',NULL,6,'Administration.manage.users(\'\')',NULL,NULL,3,'2024-07-14 17:44:14'),
(51,'System Poll',NULL,5,NULL,NULL,NULL,4,'2024-07-18 17:21:00'),
(52,'Off',51,NULL,'Heartbeat.stop()',NULL,NULL,1,'2024-07-18 17:21:19'),
(53,'On',51,NULL,'Heartbeat.reset()',NULL,NULL,2,'2024-07-18 17:21:34'),
(54,'Cache Health',NULL,5,'Administration.cache.home()',NULL,NULL,5,'2024-07-19 15:48:04');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
