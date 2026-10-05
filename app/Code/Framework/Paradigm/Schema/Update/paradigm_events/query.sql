
CREATE TABLE IF NOT EXISTS paradigm_event_types (
    id INT NOT NULL AUTO_INCREMENT,
    `namespace` CHAR(32) DEFAULT NULL,
    `event` CHAR(64) DEFAULT NULL,
    event_type_id INT DEFAULT NULL,
    `comment` CHAR(255) DEFAULT NULL,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (`namespace`,`event`,`event_type_id`)s],
);


