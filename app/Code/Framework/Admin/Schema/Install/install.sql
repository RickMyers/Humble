/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `admin_user_identification` */

DROP TABLE IF EXISTS `admin_user_identification`;

CREATE TABLE `admin_user_identification` (
  `id` int(11) NOT NULL,
  `first_name` char(96) DEFAULT NULL,
  `last_name` char(96) DEFAULT NULL,
  `middle_name` char(96) DEFAULT NULL,
  `name_suffix` char(36) DEFAULT NULL,
  `gender` char(3) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `admin_users` */

DROP TABLE IF EXISTS `admin_users`;

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` char(30) DEFAULT '',
  `password` char(13) DEFAULT '',
  `salt` char(32) DEFAULT NULL,
  `email` char(128) DEFAULT '',
  `authenticated` char(1) DEFAULT 'N',
  `new_password_token` char(16) DEFAULT '',
  `reset_password_token` char(16) DEFAULT '',
  `authentication_token` char(16) DEFAULT '',
  `logged_in` DATETIME NULL DEFAULT NULL,
  `account_status` char(1) DEFAULT '',
  `login_attempts` int(11) DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
