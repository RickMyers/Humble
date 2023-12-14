ALTER TABLE paradigm_workflow_components ADD id INT NOT NULL AUTO_INCREMENT BEFORE namespace;
DROP INDEX `PRIMARY` ON paradigm_workflow_components;
CREATE PRIMARY INDEX paradigm_workflow_components_idx ON paradigm_workflow_components(id);
CREATE UNIQUE INDEX paradigm_workflow_components_uidx ON paradigm_workflow_components(namespace,`component`,method); 
