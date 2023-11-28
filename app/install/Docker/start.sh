#!/bin/bash
service php8.2-fpm restart
php /var/www/delay_launch.php &
apachectl -D "FOREGROUND"
