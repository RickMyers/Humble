/*
SQLyog Community v12.4.3 (64 bit)
MySQL - 5.7.18-log : Database - humble
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*Table structure for table `humble_audit_log` */

CREATE TABLE humble_audit_log
(
	id INT NOT NULL AUTO_INCREMENT,
	namespace CHAR(64) DEFAULT NULL,
	controller CHAR(64) DEFAULT NULL,
	`action` CHAR(128) DEFAULT NULL,
	uid INT DEFAULT NULL,
	`identity` INT DEFAULT NULL,
	`timestamp` DATETIME DEFAULT NULL,
	modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
);

/*Table structure for table `humble_categories` */

CREATE TABLE `humble_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` char(128) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*Table structure for table `humble_chronicle` */

CREATE TABLE `humble_chronicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientid` int(11) NOT NULL,
  `uid` int(11) DEFAULT '0',
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `namespace` char(32) DEFAULT NULL,
  `class` char(64) DEFAULT NULL,
  `message` varchar(128) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `humble_chronicle_idx` (`clientid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_controllers` */

CREATE TABLE `humble_controllers` (
  `namespace` char(64) NOT NULL,
  `controller` char(64) NOT NULL,
  `compiled` char(32) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`namespace`,`controller`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_css` */

CREATE TABLE `humble_css` (
  `package` char(32) NOT NULL DEFAULT '',
  `namespace` char(32) NOT NULL DEFAULT '',
  `source` char(128) NOT NULL DEFAULT '',
  `weight` int(11) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`package`,`namespace`,`source`),
  KEY `humble_js_pkg_idx` (`package`),
  KEY `humble_js_ns_idx` (`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_edits` */

CREATE TABLE `humble_edits` (
  `namespace` char(32) NOT NULL DEFAULT '',
  `form` char(48) NOT NULL DEFAULT '',
  `source` char(128) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`namespace`,`form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_entities` */

CREATE TABLE `humble_entities` (
  `namespace` char(36) NOT NULL,
  `entity` char(128) NOT NULL,
  `polyglot` char(1) DEFAULT 'N',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`namespace`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_entity_columns` */

CREATE TABLE `humble_entity_columns` (
  `namespace` char(64) NOT NULL,
  `entity` char(64) NOT NULL,
  `column` char(64) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`namespace`,`entity`,`column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_entity_keys` */

CREATE TABLE `humble_entity_keys` (
  `namespace` char(64) NOT NULL DEFAULT '',
  `entity` char(64) NOT NULL DEFAULT '',
  `key` char(64) NOT NULL DEFAULT '',
  `auto_inc` char(1) DEFAULT 'N',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`namespace`,`entity`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_ftp_log` */

CREATE TABLE `humble_ftp_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transport` char(12) DEFAULT 'ftp',
  `host` char(128) DEFAULT NULL,
  `filename` char(254) DEFAULT NULL,
  `filesize` bigint(20) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `humble_ftp_log_idx` (`host`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_js` */

CREATE TABLE `humble_js` (
  `package` char(32) NOT NULL DEFAULT '',
  `namespace` char(32) NOT NULL DEFAULT '',
  `source` char(128) NOT NULL DEFAULT '',
  `weight` int(11) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`package`,`namespace`,`source`),
  KEY `humble_js_pkg_idx` (`package`),
  KEY `humble_js_ns_idx` (`namespace`),
  KEY `humble_edits_ns_idx` (`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_modules` */

CREATE TABLE `humble_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(64) DEFAULT NULL,
  `module` char(32) DEFAULT NULL,
  `namespace` char(64) NOT NULL,
  `configuration` varchar(128) DEFAULT NULL,
  `controller` char(64) NOT NULL DEFAULT '',
  `package` char(32) DEFAULT '',
  `version` char(10) DEFAULT '0.1.0',
  `installed` datetime DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `templater` varchar(32) NOT NULL DEFAULT '',
  `schema_install` varchar(128) DEFAULT '',
  `schema_update` varchar(128) DEFAULT '',
  `schema_layout` varchar(128) DEFAULT '',
  `models` varchar(128) NOT NULL DEFAULT '',
  `events` char(64) DEFAULT '',
  `prefix` char(32) DEFAULT '',
  `entities` varchar(255) DEFAULT '',
  `controller_cache` varchar(128) NOT NULL DEFAULT '',
  `views` varchar(128) NOT NULL DEFAULT '',
  `views_cache` varchar(128) NOT NULL DEFAULT '',
  `images` varchar(255) DEFAULT '',
  `images_cache` varchar(255) DEFAULT '',
  `rpc_mapping` varchar(255) DEFAULT '',
  `helpers` varchar(128) DEFAULT '',
  `enabled` char(1) DEFAULT 'N',
  `mongodb` varchar(64) DEFAULT '',
  `required` char(1) DEFAULT 'N',
  `weight` int(11) DEFAULT '50',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_modules_uidx` (`namespace`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*Table structure for table `humble_packages` */

CREATE TABLE `humble_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` char(128) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*Table structure for table `humble_pages` */

CREATE TABLE `humble_pages` (
  `namespace` char(32) NOT NULL DEFAULT '',
  `page` char(128) NOT NULL DEFAULT '',
  `source` varchar(128) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`namespace`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `humble_services` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `namespace` CHAR(64) DEFAULT '',
  `controller` CHAR(64) DEFAULT '',
  `action` CHAR(96) DEFAULT '',
  `output` CHAR(24) DEFAULT '',
  `view` CHAR(1) DEFAULT 'N',
  `paginated` CHAR(1) DEFAULT 'N',
  `authorized` CHAR(1) DEFAULT 'N',  
  `description` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;       

CREATE TABLE `humble_service_directory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `controller` char(64) DEFAULT NULL,
  `action` char(64) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`controller`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_templaters` */
CREATE TABLE `humble_templaters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templater` char(64) DEFAULT NULL,
  `extension` char(16) DEFAULT NULL,
  `description` char(64) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*Table structure for table `humble_templates` */

CREATE TABLE `humble_templates` (
  `namespace` char(32) NOT NULL DEFAULT '',
  `template` char(48) NOT NULL DEFAULT '',
  `source` char(128) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`namespace`,`template`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_user_identification` */

CREATE TABLE `humble_user_identification` (
  `id` int(11) NOT NULL,
  `first_name` char(96) DEFAULT NULL,
  `last_name` char(96) DEFAULT NULL,
  `middle_name` char(96) DEFAULT NULL,
  `name_suffix` char(36) DEFAULT NULL,
  `maiden_name` char(96) DEFAULT NULL,
  `gender` char(3) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_user_permissions` */

CREATE TABLE `humble_user_permissions` (
  `id` int(11) NOT NULL,
  `admin` char(1) DEFAULT 'N',
  `super_user` char(1) DEFAULT 'N',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_userlog` */

CREATE TABLE `humble_userlog` (
  `uid` int(11) NOT NULL,
  `clientid` int(11) NOT NULL,
  `timein` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeout` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `currently_viewing` int(11) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  KEY `userlog_idx` (`clientid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `humble_users` */

CREATE TABLE `humble_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` char(30) DEFAULT '',
  `password` char(13) DEFAULT '',
  `salt` char(32) DEFAULT NULL,
  `email` char(128) DEFAULT '',
  `authenticated` char(1) DEFAULT 'N',
  `security_token` char(16) DEFAULT '',
  `reset_password_token` char(16) DEFAULT '',
  `authentication_token` char(16) DEFAULT '',
  `logged_in` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `account_status` char(1) DEFAULT '',
  `login_attempts` int(11) DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
