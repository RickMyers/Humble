drop table if exists paradigm_file_triggers;
CREATE TABLE paradigm_file_triggers (
    id INT NOT NULL AUTO_INCREMENT,
    `directory` CHAR(255) DEFAULT NULL,
    `extension` CHAR(12) DEFAULT NULL,
    `field`     CHAR(64) DEFAULT NULL,
    `active`    CHAR(01) DEFAULT 'N',
    modified DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    unique key (`directory`,`extension`,`field`)
);
