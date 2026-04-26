<?php
chdir('app');
require "Humble.php";
$state = strtoupper(Environment::application('state'));
if ((!$state) || ($state == 'DEVELOPMENT') || ($state == 'DEBUG') || ($state === 'TEST')) {
    phpinfo();    
} else {
    print('Started...');
}
