<?php

/**
 * This is the "delayed launcher" that will kick off PHP-FPM after Apache2 has started
 */

$cmd      = 'ps -aux | grep -c "apachectl"';
$ctr      = 0;
$launched = false;
while ((++$ctr < 5) && !($launched)) {
    if (($result = shell_exec($cmd)) == "1") {
        exec('service php8.2-fpm start');
        die();
    } else {
        sleep(3);
    }
}
print('Did not kick off PHP-FPM');