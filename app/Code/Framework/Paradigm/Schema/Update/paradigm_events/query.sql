
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


insert  into `paradigm_event_types`(`id`,`event`,`description`,`modified`) values 
(1,'CUSTOM','Any custom event defined in code by a user','2026-09-05 18:11:59'),
(2,'TIME','An event triggered by a time/date','2026-09-05 18:12:14'),
(3,'FILE','An event watching for new/update/deletes of files','2026-09-05 18:12:32'),
(4,'ACTION','An event defined on a controller action','2026-09-05 18:12:51'),
(5,'LISTENER','Trigger a method when event is encountered','2026-09-05 18:15:07');