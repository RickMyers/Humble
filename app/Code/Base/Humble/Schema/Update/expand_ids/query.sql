
/*
  Fixing a long standing issue where the original structure from back before 2010 didn't all have IDS in them,
  which is a violation of the convention
*/

ALTER TABLE humble_controllers    ADD id INT  NOT NULL FIRST;
ALTER TABLE humble_css 		  ADD id INT  NOT NULL FIRST;
ALTER TABLE humble_js 		  ADD id INT  NOT NULL FIRST;
ALTER TABLE humble_edits 	  ADD id INT  NOT NULL FIRST;
ALTER TABLE humble_entities 	  ADD id INT  NOT NULL FIRST;
ALTER TABLE humble_entity_columns ADD id INT  NOT NULL FIRST;
ALTER TABLE humble_entity_keys    ADD id INT  NOT NULL FIRST;
ALTER TABLE paradigm_workflow_components ADD id INT  NOT NULL FIRST;

CREATE UNIQUE INDEX humble_controllers_uidx ON humble_controllers(namespace,controller);
CREATE UNIQUE INDEX humble_modules_uidx ON humble_modules(namespace);
CREATE UNIQUE INDEX humble_css_uidx ON humble_css(`package`,namespace,`source`);
CREATE UNIQUE INDEX humble_edits_uidx ON humble_edits(namespace,`form`);
CREATE UNIQUE INDEX humble_entities_uidx ON humble_entities(namespace,entity);
CREATE UNIQUE INDEX humble_entity_columns_uidx ON humble_entity_columns(namespace,entity,`column`);
CREATE UNIQUE INDEX humble_entity_keys_uidx ON humble_entity_keys(namespace,entity,`key`);
CREATE UNIQUE INDEX humble_js_uidx ON humble_js(`package`,namespace,`source`);
CREATE UNIQUE INDEX paradigm_workflow_components_uidx ON paradigm_workflow_components(namespace,`component`,`method`);
