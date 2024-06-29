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
/*Table structure for table `humble_categories` */

CREATE TABLE `humble_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `text` char(128) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_controllers` */

CREATE TABLE `humble_controllers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(64) NOT NULL,
  `controller` char(64) NOT NULL,
  `compiled` char(32) NOT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_controllers_uidx` (`namespace`,`controller`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_css` */

CREATE TABLE `humble_css` (
  `id` int NOT NULL AUTO_INCREMENT,
  `package` char(32) NOT NULL DEFAULT '',
  `namespace` char(32) NOT NULL DEFAULT '',
  `source` char(128) NOT NULL DEFAULT '',
  `weight` int DEFAULT NULL,
  `secure` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_css_uidx` (`package`,`namespace`,`source`),
  KEY `core_js_pkg_idx` (`package`),
  KEY `core_js_ns_idx` (`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_edits` */

CREATE TABLE `humble_edits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(32) NOT NULL DEFAULT '',
  `form` char(48) NOT NULL DEFAULT '',
  `source` char(128) DEFAULT NULL,
  `secure` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_edits_uidx` (`namespace`,`form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_entities` */

CREATE TABLE `humble_entities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(36) NOT NULL,
  `entity` char(128) NOT NULL,
  `polyglot` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_entities_uidx` (`namespace`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_entity_columns` */

CREATE TABLE `humble_entity_columns` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(64) NOT NULL,
  `entity` char(64) NOT NULL,
  `column` char(64) NOT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_entity_columns_uidx` (`namespace`,`entity`,`column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_entity_keys` */

CREATE TABLE `humble_entity_keys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(64) NOT NULL DEFAULT '',
  `entity` char(64) NOT NULL DEFAULT '',
  `key` char(64) NOT NULL DEFAULT '',
  `auto_inc` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_entity_keys_uidx` (`namespace`,`entity`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_js` */

CREATE TABLE `humble_js` (
  `id` int NOT NULL AUTO_INCREMENT,
  `package` char(32) NOT NULL DEFAULT '',
  `namespace` char(32) NOT NULL DEFAULT '',
  `source` char(128) NOT NULL DEFAULT '',
  `weight` int DEFAULT NULL,
  `secure` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_js_uidx` (`package`,`namespace`,`source`),
  KEY `core_js_pkg_idx` (`package`),
  KEY `core_js_ns_idx` (`namespace`),
  KEY `core_edits_ns_idx` (`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_modules` */

CREATE TABLE `humble_modules` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `weight` int DEFAULT '50',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_modules_uidx` (`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_packages` */

CREATE TABLE `humble_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `text` char(128) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_secrets_manager` */

CREATE TABLE `humble_secrets_manager` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `secret_name` char(64) DEFAULT NULL,
  `secret_value` varchar(255) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`secret_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_service_directory` */

CREATE TABLE `humble_service_directory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `controller` char(64) DEFAULT NULL,
  `action` char(64) DEFAULT NULL,
  `mapped_parameters` char(01) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`controller`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*Table structure for table `humble_service_parameters` */

CREATE TABLE `humble_service_parameters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int DEFAULT NULL,
  `parameter` char(64) DEFAULT NULL,
  `value` char(64) DEFAULT NULL,
  `source` char(24) DEFAULT NULL,
  `type` char(24) DEFAULT NULL,
  `format` char(24) DEFAULT NULL,
  `required` char(1) DEFAULT NULL,
  `default` varchar(244) DEFAULT NULL,
  `modified` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `humble_services` */

CREATE TABLE `humble_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(64) DEFAULT '',
  `controller` char(64) DEFAULT '',
  `action` char(96) DEFAULT '',
  `output` char(24) DEFAULT '',
  `view` char(1) DEFAULT 'N',
  `paginated` char(1) DEFAULT 'N',
  `authorized` char(1) DEFAULT 'N',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE humble_system_variables
(
	id INT NOT NULL AUTO_INCREMENT,
	namespace CHAR(32) DEFAULT NULL,
	`variable` CHAR(64) DEFAULT NULL,
	`value`  VARCHAR(255) DEFAULT NULL,
	modified DATETIME DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
); 

/*Table structure for table `humble_templaters` */

CREATE TABLE `humble_templaters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `templater` char(64) DEFAULT NULL,
  `extension` char(16) DEFAULT NULL,
  `description` char(64) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
