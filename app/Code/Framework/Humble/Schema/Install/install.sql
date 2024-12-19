DROP TABLE IF EXISTS `humble_audit_log`;

CREATE TABLE `humble_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(64) DEFAULT NULL,
  `controller` char(64) DEFAULT NULL,
  `action` char(128) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `identity` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_categories` */

DROP TABLE IF EXISTS `humble_categories`;

CREATE TABLE `humble_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` char(128) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_controllers` */

DROP TABLE IF EXISTS `humble_controllers`;

CREATE TABLE `humble_controllers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(64) NOT NULL,
  `controller` char(64) NOT NULL,
  `compiled` char(32) NOT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_controllers_uidx` (`namespace`,`controller`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_css` */

DROP TABLE IF EXISTS `humble_css`;

CREATE TABLE `humble_css` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package` char(32) NOT NULL DEFAULT '',
  `namespace` char(32) NOT NULL DEFAULT '',
  `source` char(128) NOT NULL DEFAULT '',
  `weight` int(11) DEFAULT NULL,
  `secure` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_css_uidx` (`package`,`namespace`,`source`),
  KEY `core_js_pkg_idx` (`package`),
  KEY `core_js_ns_idx` (`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_edits` */

DROP TABLE IF EXISTS `humble_edits`;

CREATE TABLE `humble_edits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) NOT NULL DEFAULT '',
  `form` char(48) NOT NULL DEFAULT '',
  `source` char(128) DEFAULT NULL,
  `secure` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_edits_uidx` (`namespace`,`form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_entities` */

DROP TABLE IF EXISTS `humble_entities`;

CREATE TABLE `humble_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(36) NOT NULL,
  `entity` char(128) NOT NULL,
  `actual` char(128) DEFAULT NULL,
  `alias` char(36) default null,
  `polyglot` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_entities_uidx` (`namespace`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_entity_columns` */

DROP TABLE IF EXISTS `humble_entity_columns`;

CREATE TABLE `humble_entity_columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(64) NOT NULL,
  `entity` char(64) NOT NULL,
  `column` char(64) NOT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_entity_columns_uidx` (`namespace`,`entity`,`column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_entity_keys` */

DROP TABLE IF EXISTS `humble_entity_keys`;

CREATE TABLE `humble_entity_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(64) NOT NULL DEFAULT '',
  `entity` char(64) NOT NULL DEFAULT '',
  `key` char(64) NOT NULL DEFAULT '',
  `auto_inc` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_entity_keys_uidx` (`namespace`,`entity`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_js` */

DROP TABLE IF EXISTS `humble_js`;

CREATE TABLE `humble_js` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package` char(32) NOT NULL DEFAULT '',
  `namespace` char(32) NOT NULL DEFAULT '',
  `source` char(128) NOT NULL DEFAULT '',
  `weight` int(11) DEFAULT NULL,
  `secure` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_js_uidx` (`package`,`namespace`,`source`),
  KEY `core_js_pkg_idx` (`package`),
  KEY `core_js_ns_idx` (`namespace`),
  KEY `core_edits_ns_idx` (`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_modules` */

DROP TABLE IF EXISTS `humble_modules`;

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
  `resources_js` char(64) DEFAULT NULL,
  `resources_sql` char(64) DEFAULT NULL,
  `resources_templates` char(64) DEFAULT NULL,
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
  `cli` char(01) DEFAULT 'N',
  `enabled` char(1) DEFAULT 'N',
  `mongodb` varchar(64) DEFAULT '',
  `required` char(1) DEFAULT 'N',
  `weight` int(11) DEFAULT '50',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `humble_modules_uidx` (`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_packages` */

DROP TABLE IF EXISTS `humble_packages`;

CREATE TABLE `humble_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` char(128) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_secrets_manager` */

DROP TABLE IF EXISTS `humble_secrets_manager`;

CREATE TABLE `humble_secrets_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `secret_name` char(64) DEFAULT NULL,
  `secret_value` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`secret_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_service_directory` */

DROP TABLE IF EXISTS `humble_service_directory`;

CREATE TABLE `humble_service_directory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `controller` char(64) DEFAULT NULL,
  `action` char(64) DEFAULT NULL,
  `mapped_parameters` char(1) DEFAULT 'N',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`controller`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_services` */

DROP TABLE IF EXISTS `humble_services`;

CREATE TABLE `humble_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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

/*Table structure for table `humble_system_variables` */

DROP TABLE IF EXISTS `humble_system_variables`;

CREATE TABLE `humble_system_variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` char(32) DEFAULT NULL,
  `variable` char(64) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `humble_templaters` */

DROP TABLE IF EXISTS `humble_templaters`;

CREATE TABLE `humble_templaters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templater` char(64) DEFAULT NULL,
  `extension` char(16) DEFAULT NULL,
  `description` char(64) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_user_identification` */

DROP TABLE IF EXISTS `humble_user_identification`;

CREATE TABLE `humble_user_identification` (
  `id` int(11) NOT NULL,
  `first_name` char(96) DEFAULT NULL,
  `last_name` char(96) DEFAULT NULL,
  `middle_name` char(96) DEFAULT NULL,
  `name_suffix` char(36) DEFAULT NULL,
  `maiden_name` char(96) DEFAULT NULL,
  `name` char(192) DEFAULT NULL,
  `entity` int(11) DEFAULT NULL,
  `gender` char(3) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `humble_users` */

DROP TABLE IF EXISTS `humble_users`;

CREATE TABLE `humble_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` char(30) DEFAULT '',
  `password` char(13) DEFAULT '',
  `salt` char(32) DEFAULT NULL,
  `email` char(128) DEFAULT '',
  `authenticated` char(1) DEFAULT 'N',
  `new_password_token` char(16) DEFAULT '',
  `reset_password_token` char(16) DEFAULT '',
  `authentication_token` char(16) DEFAULT '',
  `logged_in` datetime DEFAULT NULL,
  `account_status` char(1) DEFAULT '',
  `login_attempts` int(11) DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists humble_roles;
CREATE TABLE humble_roles
(
	id INT NOT NULL AUTO_INCREMENT,
	role CHAR(32) DEFAULT NULL,
	modified DATETIME DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY (role)
);

drop table if exists humble_user_roles;

CREATE TABLE humble_user_roles
(
	id INT NOT NULL AUTO_INCREMENT,
	user_id INT DEFAULT NULL,
	role_id INT DEFAULT NULL,
	modified DATETIME DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY (user_id,role_id)
);