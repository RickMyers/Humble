#!/bin/bash
service memcached start
service redis-server start
php /var/www/delay_launch.php &
apachectl -D "FOREGROUND"
