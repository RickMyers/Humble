drop table paradigm_export_targets;


create table paradigm_export_targets
(
    id int not null auto_increment,
    `alias` char(32) default null,
    target varchar(255) default null,
    token char(36) default null,
    modified datetime default current_timestamp,
    primary key (id)
);