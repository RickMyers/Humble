drop table blog_whatsnew;
CREATE TABLE blog_whatsnew
(
       id INT NOT NULL AUTO_INCREMENT,
       `version` CHAR(10) DEFAULT NULL,
       `title` char(128) default null,
       article TEXT DEFAULT NULL,
       author INT DEFAULT NULL,
       `active` CHAR(01) DEFAULT 'N',
       published DATE DEFAULT NULL,
       modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       PRIMARY KEY (id)
);
