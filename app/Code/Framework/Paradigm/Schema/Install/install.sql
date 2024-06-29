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
/*Table structure for table `paradigm_api_projects` */

CREATE TABLE `paradigm_api_projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` char(64) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_api_tests` */

CREATE TABLE `paradigm_api_tests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int DEFAULT NULL,
  `name` char(64) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `details` json DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_designer_forms` */

CREATE TABLE `paradigm_designer_forms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` char(32) DEFAULT NULL,
  `image` longblob,
  `image_name` char(64) DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_event_listeners` */

CREATE TABLE `paradigm_event_listeners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `event` char(128) DEFAULT NULL,
  `workflow_id` char(32) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event` (`event`,`namespace`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_event_log` */

CREATE TABLE `paradigm_event_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mongo_id` char(24) DEFAULT NULL,
  `event` char(64) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_events` */

CREATE TABLE `paradigm_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `event` char(128) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_events_uidx` (`namespace`,`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_export_targets` */

CREATE TABLE `paradigm_export_targets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `alias` char(32) DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  `token` char(36) DEFAULT NULL,
  `modified` datetime  NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `paradigm_import_sources` */

CREATE TABLE `paradigm_import_sources` (
  `id` int NOT NULL AUTO_INCREMENT,
  `alias` char(32) DEFAULT NULL,
  `source` char(255) DEFAULT NULL,
  `token` char(32) DEFAULT NULL,
  `modified` datetime  NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `paradigm_import_tokens` */

CREATE TABLE `paradigm_import_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(255) DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_job_queue` */

CREATE TABLE `paradigm_job_queue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `system_event_id` int DEFAULT NULL,
  `queued` datetime DEFAULT NULL,
  `started` datetime DEFAULT NULL,
  `pid` int DEFAULT NULL,
  `finished` datetime DEFAULT NULL,
  `status` char(1) DEFAULT 'N',
  `comment` varchar(255) DEFAULT NULL,
  `modified` datetime  NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paradigm_job_queue_idx` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_method_listeners` */

CREATE TABLE `paradigm_method_listeners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `event` char(64) DEFAULT NULL,
  `class` char(64) DEFAULT NULL,
  `method` char(64) DEFAULT NULL,
  `active` char(1) DEFAULT 'Y',
  `modified` datetime  NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`event`,`class`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `paradigm_scheduler_log` */

CREATE TABLE `paradigm_scheduler_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `started` datetime DEFAULT NULL,
  `finished` datetime DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_scheduler_log_uidx` (`started`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_sensor_workflows` */

CREATE TABLE `paradigm_sensor_workflows` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sensor_id` int NOT NULL,
  `workflow_id` char(32) DEFAULT NULL,
  `value` char(128) DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_webservice_workflows_uidx` (`workflow_id`,`value`),
  UNIQUE KEY `paradigm_sensor_workflows_uidx` (`workflow_id`,`value`),
  KEY `paradigm_webservice_workflows_uri_idx` (`value`),
  KEY `paradigm_webservice_workflows_wid_idx` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_sensors` */

CREATE TABLE `paradigm_sensors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sensor` char(64) DEFAULT '',
  `sensor_id` char(32) DEFAULT '' COMMENT 'The MongoDB Id for the object containing the service information',
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_sensors_uidx` (`sensor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_system_events` */

CREATE TABLE `paradigm_system_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `workflow_id` char(32) DEFAULT NULL,
  `event_start` datetime DEFAULT NULL,
  `recurring` char(1) DEFAULT 'N',
  `period` char(32) DEFAULT '0',
  `last_run` datetime  NULL DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_system_events_uidx` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_webhook_workflows` */

CREATE TABLE `paradigm_webhook_workflows` (
  `id` int NOT NULL AUTO_INCREMENT,
  `webhook_id` int DEFAULT NULL,
  `workflow_id` char(32) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`webhook_id`,`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_webhooks` */

CREATE TABLE `paradigm_webhooks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `webhook` char(128) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `format` char(32) DEFAULT NULL,
  `field` char(32) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webhook` (`webhook`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_webservice_workflows` */

CREATE TABLE `paradigm_webservice_workflows` (
  `id` int NOT NULL AUTO_INCREMENT,
  `webservice_id` int NOT NULL,
  `workflow_id` char(32) DEFAULT NULL,
  `uri` char(128) DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_webservice_workflows_uidx` (`workflow_id`,`uri`),
  KEY `paradigm_webservice_workflows_uri_idx` (`uri`),
  KEY `paradigm_webservice_workflows_wid_idx` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_webservices` */

CREATE TABLE `paradigm_webservices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uri` char(64) DEFAULT '',
  `webservice_id` char(32) DEFAULT '' COMMENT 'The MongoDB Id for the object containing the service information',
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_webservices_idx` (`uri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_workflow_comments` */

CREATE TABLE `paradigm_workflow_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT '',
  `class` char(64) DEFAULT '',
  `method` char(64) DEFAULT '',
  `comment` varchar(1024) DEFAULT '',
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`class`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_workflow_components` */

CREATE TABLE `paradigm_workflow_components` (
  `id` int NOT NULL AUTO_INCREMENT,
  `namespace` char(32) NOT NULL,
  `component` char(64) NOT NULL,
  `method` char(128) NOT NULL,
  `event` char(1) DEFAULT 'N',
  `process` char(1) DEFAULT 'N',
  `decision` char(1) DEFAULT 'N',
  `rule` char(1) DEFAULT 'N',
  `sensor` char(1) DEFAULT 'N',
  `program` char(1) DEFAULT 'N',
  `input` char(1) DEFAULT 'N',
  `service` char(1) DEFAULT 'N',
  `notification` char(1) DEFAULT 'N',
  `report` char(1) DEFAULT 'N',
  `authorization` char(1) DEFAULT 'N',
  `event_name` char(64) DEFAULT NULL,
  `configuration` char(64) DEFAULT NULL,
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_workflow_components_uidx` (`namespace`,`component`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_workflow_listeners` */

CREATE TABLE `paradigm_workflow_listeners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `workflow_id` char(32) NOT NULL DEFAULT '',
  `namespace` char(32) NOT NULL DEFAULT '',
  `component` char(32) NOT NULL DEFAULT '',
  `method` char(64) NOT NULL DEFAULT '',
  `active` char(1) DEFAULT 'N',
  `modified` datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_workflows` */

CREATE TABLE `paradigm_workflows` (
  `id` int NOT NULL AUTO_INCREMENT,
  `workflow_id` char(32) DEFAULT '' COMMENT 'The ID of the Start element, which will become the name of the workflow',
  `creator` int DEFAULT NULL,
  `major_version` int DEFAULT '1',
  `minor_version` int DEFAULT '0',
  `title` varchar(128) DEFAULT '',
  `description` varchar(512) DEFAULT '',
  `image` mediumblob,
  `saved` datetime  NULL DEFAULT NULL,
  `generated` datetime  NULL DEFAULT NULL,
  `generated_workflow_id` char(32) DEFAULT '',
  `workflow` mediumtext,
  `namespace` char(32) DEFAULT NULL,
  `partial` char(1) DEFAULT 'N',
  `active` char(1) DEFAULT 'N',
  `modified` datetime  NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_workflows_idx` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
