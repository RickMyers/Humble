CREATE TABLE &&NAMESPACE&&_roles
(
	id INT NOT NULL AUTO_INCREMENT,
	role CHAR(32) DEFAULT NULL,
	modified DATETIME DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY (role)
);
CREATE TABLE &&NAMESPACE&&_user_roles
(
	id INT NOT NULL AUTO_INCREMENT,
	user_id INT DEFAULT NULL,
	role_id INT DEFAULT NULL,
	modified DATETIME DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY (user_id,role_id)
);
