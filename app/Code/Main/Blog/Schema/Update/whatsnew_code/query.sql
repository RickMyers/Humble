 CREATE TABLE blog_whatsnew_code
 (
	id INT NOT NULL AUTO_INCREMENT,
	article_id  INT DEFAULT NULL,
	language_id INT DEFAULT NULL,
	lexicon_id  INT DEFAULT NULL,		
	`sample`  CHAR(255) DEFAULT NULL,
	`scroll`  CHAR(16) DEFAULT '100%',
	`height`  CHAR(16) DEFAULT '200px',
	modified datetime default CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
 );
 
 CREATE TABLE blog_whatsnew_languages
 (
	id INT NOT NULL AUTO_INCREMENT,
	`language` CHAR(32) DEFAULT NULL,
	modified datetime default CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
 
 );
 
 CREATE TABLE blog_whatsnew_lexicons
 (
	id INT NOT NULL AUTO_INCREMENT,
	`lexicon` CHAR(255) DEFAULT NULL,
	`description` CHAR(255) DEFAULT NULL,
	modified datetime default CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
 
 );
