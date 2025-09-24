<?php

/**
 * This is the "delayed launcher" that will kick off PHP-FPM after Apache2 has started
 */

$cmd      = 'ps -aux | grep -c "apachectl"';
$ctr      = 0;
$launched = false;
chdir('/var/www/');
while ((++$ctr < 10) && !($launched)) {
    if ((int)($result = shell_exec($cmd)) >= 2) {
        exec('service php8.2-fpm start');
        exec('service memcached start');
        exec('chown -R www-data:root html');
        exec('chmod -R 0775 html');
        file_put_contents('results.txt','I launched PHP-FPM');
        die();
    } else {
        sleep(3);
    }
}
file_put_contents('results.txt','Did not kick off PHP-FPM');
print('Did not kick off PHP-FPM');