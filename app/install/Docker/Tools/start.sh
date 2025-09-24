#!/bin/bash
service memcached start
service redis-server start
rm /run/apache2/apache2.pid
php /var/www/delay_launch.php &
apachectl -D "FOREGROUND"
