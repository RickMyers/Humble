/*
SQLyog Community v13.3.1 (64 bit)
MySQL - 5.7.44 : Database - humble
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(64) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_api_tests` */

CREATE TABLE `paradigm_api_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `name` char(64) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `details` json DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_designer_forms` */

CREATE TABLE `paradigm_designer_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) DEFAULT NULL,
  `image` longblob,
  `image_name` char(64) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_event_listeners` */

CREATE TABLE `paradigm_event_listeners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `event` char(128) DEFAULT NULL,
  `workflow_id` char(32) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event` (`event`,`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_event_log` */

CREATE TABLE `paradigm_event_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mongo_id` char(24) DEFAULT NULL,
  `event` char(64) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_event_types` */

CREATE TABLE `paradigm_event_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` char(64) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_events` */

CREATE TABLE `paradigm_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `event` char(64) DEFAULT NULL,
  `event_type_id` int(11) DEFAULT NULL,
  `comment` char(255) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`event`,`event_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_export_targets` */

CREATE TABLE `paradigm_export_targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` char(32) DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  `token` char(36) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_file_log` */

CREATE TABLE `paradigm_file_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) DEFAULT NULL,
  `directory` char(255) DEFAULT NULL,
  `file` char(255) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_file_triggers` */

CREATE TABLE `paradigm_file_triggers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `directory` char(255) DEFAULT NULL,
  `extension` char(12) DEFAULT NULL,
  `field` char(64) DEFAULT NULL,
  `workflow_id` int(11) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `directory` (`directory`,`extension`,`field`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_import_sources` */

CREATE TABLE `paradigm_import_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` char(32) DEFAULT NULL,
  `source` char(255) DEFAULT NULL,
  `token` char(32) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_import_tokens` */

CREATE TABLE `paradigm_import_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_job_queue` */

CREATE TABLE `paradigm_job_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `system_event_id` int(11) DEFAULT NULL,
  `workflow_id` int(11) DEFAULT NULL,
  `queued` datetime DEFAULT NULL,
  `started` datetime DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `finished` datetime DEFAULT NULL,
  `status` char(1) DEFAULT 'N',
  `comment` varchar(255) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paradigm_job_queue_idx` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_method_listeners` */

CREATE TABLE `paradigm_method_listeners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `event` char(64) DEFAULT NULL,
  `class` char(64) DEFAULT NULL,
  `method` char(64) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`event`,`class`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_system_events` */

CREATE TABLE `paradigm_system_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` char(32) DEFAULT NULL,
  `event_start` datetime DEFAULT NULL,
  `recurring` char(1) DEFAULT 'N',
  `period` char(32) DEFAULT '0',
  `last_queued` datetime DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_system_events_uidx` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_webhook_workflows` */

CREATE TABLE `paradigm_webhook_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webhook_id` int(11) DEFAULT NULL,
  `workflow_id` char(32) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`webhook_id`,`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_webhooks` */

CREATE TABLE `paradigm_webhooks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `webhook` char(128) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `format` char(32) DEFAULT NULL,
  `field` char(32) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webhook` (`webhook`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_webservice_uris` */

CREATE TABLE `paradigm_webservice_uris` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `uri` char(64) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`uri`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_webservice_workflows` */

CREATE TABLE `paradigm_webservice_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) DEFAULT NULL,
  `uri_id` int(11) DEFAULT NULL,
  `enabled` char(1) DEFAULT 'N',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workflow_id` (`workflow_id`,`uri_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_webservices` */

CREATE TABLE `paradigm_webservices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri_id` int(11) DEFAULT NULL,
  `workflow_id` int(11) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri_id` (`uri_id`,`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_workflow_component_types` */

CREATE TABLE `paradigm_workflow_component_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` char(64) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `component` (`component`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_workflow_components` */

CREATE TABLE `paradigm_workflow_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `component` char(64) DEFAULT NULL,
  `component_type_id` int(11) DEFAULT NULL,
  `method` char(128) DEFAULT NULL,
  `event_name` char(64) DEFAULT NULL,
  `configuration` char(128) DEFAULT NULL,
  `comment` varchar(1024) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`component`,`component_type_id`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `paradigm_workflow_listeners` */

CREATE TABLE `paradigm_workflow_listeners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` char(32) NOT NULL DEFAULT '',
  `namespace` char(32) NOT NULL DEFAULT '',
  `component` char(32) NOT NULL DEFAULT '',
  `method` char(64) NOT NULL DEFAULT '',
  `active` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_workflows` */

CREATE TABLE `paradigm_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mongo_id` char(32) DEFAULT '' COMMENT 'The ID of the Start element, which will become the name of the workflow',
  `creator` int(11) DEFAULT NULL,
  `major_version` int(11) DEFAULT '1',
  `minor_version` int(11) DEFAULT '0',
  `title` varchar(128) DEFAULT '',
  `description` varchar(512) DEFAULT '',
  `image` mediumblob,
  `saved` datetime DEFAULT NULL,
  `generated` datetime DEFAULT NULL,
  `generated_workflow_id` char(32) DEFAULT '',
  `workflow` mediumtext,
  `namespace` char(32) DEFAULT NULL,
  `partial` char(1) DEFAULT 'N',
  `active` char(1) DEFAULT 'N',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_workflows_idx` (`mongo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
