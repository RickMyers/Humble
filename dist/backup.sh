#!/bin/bash
echo $PWD
now=$(date +"%Y%m%d")
ts="D_$now"
[ -d "/var/www/Backups/$ts" ] || mkdir "/var/www/Backups/$ts"
cd "/var/www/Backups/$ts"
mongodump
mysqldump --all-databases -u root -p > mysql.sql