<?php

//#######################################################
//User Account Status
define('HUMBLE_USER_ACCOUNT_LOCKED','L');
define('HUMBLE_USER_ACCOUNT_SUSPENDED','S');
define('HUMBLE_USER_ACCOUNT_UNLOCKED','');

//#######################################################
//Job Queue Status
define('HUMBLE_NEW_EVENT_JOB','N');
define('HUMBLE_NEW_FILE_JOB','L');
define('HUMBLE_JOB_EXECUTING','E');
define('HUMBLE_JOB_COMPLETED','C');
define('HUMBLE_JOB_FAILED','F');

//#######################################################
//Workflow Event Types
define('HUMBLE_CUSTOM_EVENT',1);
define('HUMBLE_TIME_EVENT',2);
define('HUMBLE_FILE_EVENT',3);
define('HUMBLE_ACTION_EVENT',4);
define('HUMBLE_LISTENER_EVENT',5);
define('HUMBLE_WEBHOOK_EVENT',6);
define('HUMBLE_SERVICE_EVENT',7);

//#######################################################
//Workflow Component Types
define('HUMBLE_EVENT',1);
define('HUMBLE_PROCESS',2);
define('HUMBLE_DECISION',3);
define('HUMBLE_GENERATOR',4);
define('HUMBLE_RULE',5);
define('HUMBLE_SENSOR',6);
define('HUMBLE_PROGRAM',7);
define('HUMBLE_INPUT',8);
define('HUMBLE_SERVICE',9);
define('HUMBLE_NOTIFICATION',10);
define('HUMBLE_REPORT',11);
define('HUMBLE_AUTHORIZATION',12);
