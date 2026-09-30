<?php

//#######################################################
//User Account Status
define('USER_ACCOUNT_LOCKED','L');
define('USER_ACCOUNT_SUSPENDED','S');
define('USER_ACCOUNT_UNLOCKED','');

//#######################################################
//Job Queue Status
define('NEW_EVENT_JOB','N');
define('NEW_FILE_JOB','L');
define('JOB_EXECUTING','E');
define('JOB_COMPLETED','C');
define('JOB_FAILED','F');

//#######################################################
//Event Types
define('CUSTOM_EVENT',1);
define('TIME_EVENT',2);
define('FILE_EVENT',3);
define('ACTION_EVENT',4);
define('LISTENER',5);
