CREATE TABLE paradigm_event_listeners
(
	id INT NOT NULL AUTO_INCREMENT,
	`namespace` CHAR(32) DEFAULT NULL,
	`event` CHAR (128) DEFAULT NULL,
	workflow_id CHAR(32) DEFAULT NULL,
	active CHAR(01) DEFAULT 'N',
	modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY (`event`),
	INDEX (`event`)
);
