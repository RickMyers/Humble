DROP TABLE IF EXISTS `admin_menu_categories`;

CREATE TABLE `admin_menu_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` char(32) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `admin_system_monitor` */

DROP TABLE IF EXISTS `admin_system_monitor`;

CREATE TABLE `admin_system_monitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cpu` int(11) DEFAULT NULL,
  `utilization` float DEFAULT NULL,
  `total_threads` int(11) DEFAULT NULL,
  `apache_threads` int(11) DEFAULT NULL,
  `fpm_threads` int(11) DEFAULT NULL,
  `server_load` float DEFAULT NULL,
  `memcached` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  `logged_in` datetime DEFAULT NULL,
  `account_status` char(1) DEFAULT '',
  `login_attempts` int(11) DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
