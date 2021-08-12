             
             ALTER TABLE humble_css ADD secure CHAR (01) DEFAULT 'N' AFTER weight;
             ALTER TABLE humble_js ADD secure CHAR (01) DEFAULT 'N' AFTER weight;
             ALTER TABLE humble_edits ADD secure CHAR (01) DEFAULT 'N' AFTER source;
