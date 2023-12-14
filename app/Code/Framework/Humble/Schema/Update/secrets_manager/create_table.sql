

create table humble_secrets_manager
(
    id int not null auto_increment,
    namespace char(32) default null,
    secret_name char(64) default null,
    secret_value varchar(255) default null,
    user_id int default null,
    modified datetime default current_timestamp,
    primary key (id),
    unique key (namespace,secret_name)
);
