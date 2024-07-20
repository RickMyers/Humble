drop table if exists paradigm_file_log;
CREATE TABLE paradigm_file_log (
	id INT NOT NULL AUTO_INCREMENT,
	`workflow_id` int DEFAULT NULL,
	`directory` CHAR(255) DEFAULT NULL,
	`file` CHAR(255) DEFAULT NULL,
	modified DATETIME DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
);
