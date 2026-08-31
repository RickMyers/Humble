CREATE TABLE `account_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_number` char(20) DEFAULT NULL,
  `email` char(128) DEFAULT NULL,
  `first_name` char(48) DEFAULT NULL,
  `last_name` char(48) DEFAULT NULL,
  `factory_name` char(48) DEFAULT NULL,
  `project` char(128) DEFAULT NULL,
  `project_url` char(255) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `serial_number_2` (`serial_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `account_users` like `humble_users`;

create table `account_user_identification` like `humble_user_identification`;

alter table `account_users` add `serial_number` char(20) default null after `id`;

create unique index `account_users_idx` on `account_users` (serial_number,user_name);