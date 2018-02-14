#!/bin/bash

# NOTE:  This assumes that the user 'codeship' was used to deploy the code to this server
# NOTE:  This assumes that the apache server is running under user 'www-data'
#        Adjust the above accordingly...
#
# $1 is the action
# $2 is project
# $3 is the timestamp
# $4 is the DB Userid
# $5 is the DB Password
#create,backup,cleanup,remove,deploy,retain,rename

case $1 in
  'open')
        if [ -d "/var/www/$2" ]; then
                chown -R codeship:www-data /var/www/$2
                chmod -R 0775 /var/www/$2
        fi
        if [ -d "/var/www/$2_old" ]; then
                chown -R codeship:www-data /var/www/$2_old
                chmod -R 0775 /var/www/$2_old
        fi
        if [ -d "/var/www/Docs/$2" ]; then
                chown -R codeship:www-data /var/www/Docs/$2
                chmod -R 0775 /var/www/Docs/$2
        fi
        echo 'open';;
  'create')
        mkdir /var/www/$2_$3
        echo 'create';;
  'backup')
        echo $PWD
        ts=D_$3
        [ -d /var/www/backups/$2/$ts ] || mkdir /var/www/backups/$2/$3
        cd /var/www/backups/$2/$3
        mongodump
        mysqldump --all-databases -u $4 -p$5 > mysql.sql
        echo 'backup';;
  'cleanup')
        php cleanup.php $2 $3
        echo 'cleanup';;
  'remove')
        rm -R /var/www/$2_old
        echo 'remove';;
  'retain')
        mv /var/www/$2 /var/www/$2_old
        echo 'retain';;
  'rename')
        mv /var/www/$2_$3 /var/www/$2
        chown -R codeship:www-data /var/www/$2
        chmod -R 0775 /var/www/$2
        echo 'rename';;
  'composer')
        cd /var/www/$2/app
        composer update;;
  'update')
        cd /var/www/$2/app
        php Module.php --u ns=*
        echo 'update';;
  'package')
        cd /var/www/$2/app
        php Module.php --package
        echo 'package';;
  'assign')
        cd /var/www
        chmod -R 0775 $2
        chown -R codeship:www-data $2
        echo 'assign';;
  'document')
        rm -R /var/www/Docs/$2
        mkdir /var/www/Docs/$2
        chmod -R 0775 /var/www/Docs/$2
        chown codeship:www-data /var/www/Docs/$2
        cd /var/www/Docs/$2
        /var/www/phpdoc/vendor/bin/phpdoc -c /var/www/$2/phpdoc.dist.xml
        echo 'document';;
  'increment')
        cd /var/www/$2/app
        php Module.php --increment
        echo 'package';;
  *)
        echo "i dunno how to do that $1";;
esac
