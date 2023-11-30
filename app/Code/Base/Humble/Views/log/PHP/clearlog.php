<?php
    //This is just an example of using PHP for a view... not really recommended
    $log->clearLog();
    print('Cleared the '.$log->getLog().' log for '.$user->getFirstName().' '.$user->getLastName());

?>