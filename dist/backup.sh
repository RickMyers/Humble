#!/bin/bash
echo $PWD
now=$(date +"%Y%m%d")
ts="D_$now"
[ -d "/var/www/backups/$ts" ] || mkdir "/var/www/backups/$ts"
cd "/var/www/backups/$ts"
mongodump
mysqldump --all-databases -u root -p > mysql.sql