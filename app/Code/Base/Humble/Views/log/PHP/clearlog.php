<?php
    if ($permissions->getSuperUser()=='Y') {
        $log->clearLog();
        print('Cleared the '.$log->getLog().' log for '.$user->getFirstName().' '.$user->getLastName());
    } else {
        print($user->getFirstName().' '.$user->getLastName().' lacks the required authority to clear the '.$log->getLog().' log.');
    }

?>