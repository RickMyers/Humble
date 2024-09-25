DROP TABLE IF EXISTS `account_registrations`;

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