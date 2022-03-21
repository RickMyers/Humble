<?php
chdir('app');
require "Humble.php";

if (Environment::getApplication('state')=='DEVELOPMENT') {
    phpinfo();    
} else {
    print('Started...');
}


?>
