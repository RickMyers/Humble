            DROP TABLE paradigm_webservices;
            CREATE TABLE paradigm_webservices
            (
		id INT NOT NULL AUTO_INCREMENT,
		webservice CHAR(128) DEFAULT NULL,
		component_id CHAR(38) DEFAULT NULL,
		`active` CHAR(01) DEFAULT 'N',
		modified DATETIME DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY (webservice)
            );
            
            DROP TABLE paradigm_webservice_workflows;
            CREATE TABLE paradigm_webservice_workflows
            (
		id INT NOT NULL AUTO_INCREMENT,
		webservice_id INT DEFAULT NULL,
		workflow_id INT DEFAULT NULL,
		`active` CHAR(01) DEFAULT 'N',
		modified DATETIME DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id)
            );
            
            SELECT * 
              FROM paradigm_webservices AS a
              LEFT OUTER JOIN paradigm_webservice_workflows AS b
                ON a.id = b.webservice_id
              LEFT OUTER JOIN paradigm_workflows AS c
                ON b.workflow_id = c.id
              
