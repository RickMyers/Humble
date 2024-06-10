

create table admin_system_monitor
(
    id int not null auto_increment,
    cpu int default null,
    utilization float default null,
    total_threads int default null,
    apache_threads int default null,
    fpm_threads int default null,
    server_load float default null,
    memcached int default null,
    modified datetime default current_timestamp,
    primary key (id)
);
