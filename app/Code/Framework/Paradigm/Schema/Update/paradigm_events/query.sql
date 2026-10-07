DROP TABLE paradigm_event_types;

CREATE TABLE IF NOT EXISTS paradigm_event_types (
    id INT NOT NULL AUTO_INCREMENT,
    `event_type` CHAR(64) DEFAULT NULL,
    `description` CHAR(255) DEFAULT NULL,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (event_type)
);


INSERT  INTO `paradigm_event_types`(`id`,`event_type`,`description`,`modified`) VALUES 
(1,'CUSTOM','Any custom event defined in code by a user','2026-09-05 18:11:59'),
(2,'TIME','An event triggered by a time/date','2026-09-05 18:12:14'),
(3,'FILE','An event watching for new/update/deletes of files','2026-09-05 18:12:32'),
(4,'ACTION','An event defined on a controller action','2026-09-05 18:12:51'),
(5,'LISTENER','Trigger a method when event is encountered','2026-09-05 18:15:07');