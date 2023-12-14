
drop table paradigm_import_sources;

create table paradigm_import_sources
(
    id int not null auto_increment,
    alias char(32) default null,
    source char(255) default null,
    `token` char(32) default null,
    modified datetime default current_timestamp,
    primary key (id)
);
