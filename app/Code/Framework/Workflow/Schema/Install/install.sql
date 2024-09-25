DROP TABLE IF EXISTS `workflow_sms_carriers`;

CREATE TABLE `workflow_sms_carriers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier` char(32) DEFAULT NULL,
  `sms_domain` char(64) DEFAULT NULL,
  `mms_domain` char(64) DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carrier` (`carrier`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;