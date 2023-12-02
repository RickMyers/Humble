#!/bin/bash
php /var/www/delay_launch.php &
apachectl -D "FOREGROUND"
