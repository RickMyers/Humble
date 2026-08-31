create table `account_users` like `humble_users`;

create table `account_user_identification` like `humble_user_identification`;

alter table `account_users` add `serial_number` char(20) default null after `id`;

create unique index `account_users_idx` on `account_users` (serial_number,user_name);
