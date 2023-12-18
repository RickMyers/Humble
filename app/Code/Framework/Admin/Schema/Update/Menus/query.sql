

create table admin_menu_categories
(
    id int not null auto_increment,
    category char(32) default null,
    seq int default null,
    modified datetime default current_timestamp,
    primary key (id)
);
drop table admin_menus;
create table admin_menus
(
    id int not null auto_increment,
    menu char(48) default null,
    parent_id int default null,
    category_id int default null,
    `function` char(255) default null,
    seq int default null,
    modified datetime default current_timestamp,
    primary key (id)
);
