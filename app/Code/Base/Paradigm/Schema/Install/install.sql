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
/*Table structure for table `paradigm_designer_forms` */

CREATE TABLE `paradigm_designer_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) DEFAULT NULL,
  `image` longblob,
  `image_name` char(64) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_event_log` */

CREATE TABLE `paradigm_event_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mongo_id` char(24) DEFAULT NULL,
  `event` char(64) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_events` */

CREATE TABLE `paradigm_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `event` char(128) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_events_uidx` (`namespace`,`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_import_sources` */

CREATE TABLE `paradigm_import_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) DEFAULT NULL,
  `source` varchar(255) DEFAULT '',
  `token` char(32) DEFAULT 'null' COMMENT 'Security Token',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_job_queue` */

CREATE TABLE `paradigm_job_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `system_event_id` int(11) DEFAULT NULL,
  `queued` datetime DEFAULT NULL,
  `started` datetime DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `finished` datetime DEFAULT NULL,
  `status` char(1) DEFAULT 'N',
  `comment` varchar(255) DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paradigm_job_queue_idx` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_scheduler_log` */

CREATE TABLE `paradigm_scheduler_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `started` datetime DEFAULT NULL,
  `finished` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_scheduler_log_uidx` (`started`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_sensor_workflows` */

CREATE TABLE `paradigm_sensor_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sensor_id` int(11) NOT NULL,
  `workflow_id` char(32) DEFAULT NULL,
  `value` char(128) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_webservice_workflows_uidx` (`workflow_id`,`value`),
  UNIQUE KEY `paradigm_sensor_workflows_uidx` (`workflow_id`,`value`),
  KEY `paradigm_webservice_workflows_uri_idx` (`value`),
  KEY `paradigm_webservice_workflows_wid_idx` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_sensors` */

CREATE TABLE `paradigm_sensors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sensor` char(64) DEFAULT '',
  `sensor_id` char(32) DEFAULT '' COMMENT 'The MongoDB Id for the object containing the service information',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_sensors_uidx` (`sensor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_system_events` */

CREATE TABLE `paradigm_system_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` char(32) DEFAULT NULL,
  `event_start` datetime DEFAULT NULL,
  `recurring` char(1) DEFAULT 'N',
  `period` char(32) DEFAULT '0',
  `last_run` timestamp NULL DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_system_events_uidx` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE paradigm_webhooks
(
    id INT NOT NULL AUTO_INCREMENT,
    namespace CHAR(32) DEFAULT NULL,
    hook CHAR(64) DEFAULT NULL,
    `format` CHAR(32) DEFAULT 'JSON',
    active CHAR(01) DEFAULT 'N',
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (namespace, hook)
);

/*Table structure for table `paradigm_webservice_workflows` */

CREATE TABLE `paradigm_webservice_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webservice_id` int(11) NOT NULL,
  `workflow_id` char(32) DEFAULT NULL,
  `uri` char(128) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_webservice_workflows_uidx` (`workflow_id`,`uri`),
  KEY `paradigm_webservice_workflows_uri_idx` (`uri`),
  KEY `paradigm_webservice_workflows_wid_idx` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_webservices` */

CREATE TABLE `paradigm_webservices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` char(64) DEFAULT '',
  `webservice_id` char(32) DEFAULT '' COMMENT 'The MongoDB Id for the object containing the service information',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_webservices_idx` (`uri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_workflow_comments` */

CREATE TABLE `paradigm_workflow_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT '',
  `class` char(64) DEFAULT '',
  `method` char(64) DEFAULT '',
  `comment` varchar(1024) DEFAULT '',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`class`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_workflow_components` */

CREATE TABLE `paradigm_workflow_components` (
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
  PRIMARY KEY (`namespace`,`component`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_workflow_listeners` */

CREATE TABLE `paradigm_workflow_listeners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` char(32) NOT NULL DEFAULT '',
  `namespace` char(32) NOT NULL DEFAULT '',
  `component` char(32) NOT NULL DEFAULT '',
  `method` char(64) NOT NULL DEFAULT '',
  `active` char(1) DEFAULT 'N',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `paradigm_workflows` */

CREATE TABLE `paradigm_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` char(32) DEFAULT '' COMMENT 'The ID of the Start element, which will become the name of the workflow',
  `creator` int(11) DEFAULT NULL,
  `major_version` int(11) DEFAULT '1',
  `minor_version` int(11) DEFAULT '0',
  `title` varchar(128) DEFAULT '',
  `description` varchar(512) DEFAULT '',
  `image` mediumblob,
  `saved` timestamp NULL DEFAULT NULL,
  `generated` timestamp NULL DEFAULT NULL,
  `generated_workflow_id` char(32) DEFAULT '',
  `workflow` mediumtext,
  `namespace` char(32) DEFAULT NULL,
  `partial` char(1) DEFAULT 'N',
  `active` char(1) DEFAULT 'N',
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paradigm_workflows_idx` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `paradigm_event_listeners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `event` char(128) DEFAULT NULL,
  `workflow_id` char(32) DEFAULT NULL,
  `active` char(1) DEFAULT 'N',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event` (`event`),
  KEY `event_2` (`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
