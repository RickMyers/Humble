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
