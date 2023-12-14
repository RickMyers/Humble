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
/*Data for the table `workflow_sms_carriers` */

insert  into `workflow_sms_carriers`(`id`,`carrier`,`sms_domain`,`mms_domain`,`modified`) values 
(1,'AllTel(NULL)','message.alltel.com','mms.alltelwireless.com','2018-03-03 17:30:31'),
(2,'AT&T','txt.att.net','mms.att.net','2018-03-03 17:30:48'),
(3,'Boost Mobile','myboostmobile.com','myboostmobile.com','2018-03-03 17:31:18'),
(4,'Cricket Wireless',NULL,'mms.cricketwireless.net','2018-03-03 17:31:35'),
(5,'Project Fi',NULL,'msg.fi.google.com','2018-03-03 17:31:47'),
(6,'Sprint','messaging.sprintpcs.com','pm.sprint.com','2018-03-03 17:32:08'),
(7,'T-Mobile','tmomail.net','tmomail.net','2018-03-03 17:32:46'),
(8,'U.S. Cellular','email.uscc.net','mms.uscc.net','2018-03-03 17:33:46'),
(9,'Verizon','vtext.com','vzwpix.com','2018-03-03 17:34:09'),
(10,'Virgin Mobile','vmobl.com','vmpix.com','2018-03-03 17:34:31'),
(11,'Republic Wireless','text.republicwireless.com',NULL,'2018-03-03 17:34:43');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
