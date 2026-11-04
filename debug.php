<?php
chdir('app');
require_once 'Humble.php';
$state = strtoupper(Environment::application('state'));
if ((!$state) || ($state == 'DEVELOPMENT') || ($state == 'DEBUG') || ($state === 'TEST')) {
    phpinfo();    
} else {
    print('Started...');
}
