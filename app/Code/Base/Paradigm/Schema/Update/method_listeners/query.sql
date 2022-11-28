 CREATE TABLE paradigm_method_listeners
 (
	id INT NOT NULL AUTO_INCREMENT,
	namespace CHAR(32) DEFAULT NULL,
	`event` CHAR(64) DEFAULT NULL,
	class CHAR(64) DEFAULT NULL,
	method CHAR(64) DEFAULT NULL,
        modified timestamp default current_timestamp,
	PRIMARY KEY (id),
	UNIQUE KEY (namespace,`event`,class,method)
 );
