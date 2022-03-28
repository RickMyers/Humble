<?php
chdir('app');
require "Humble.php";
$state = strtoupper(Environment::getApplication('state'));
if (($state == 'DEVELOPMENT') || ($state == 'DEBUG')) {
    phpinfo();    
} else {
    print('Started...');
}
