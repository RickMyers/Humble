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
//Workflow Event Types
define('CUSTOM_EVENT',1);
define('TIME_EVENT',2);
define('FILE_EVENT',3);
define('ACTION_EVENT',4);
define('LISTENER',5);
define('WEBHOOK',6);
define('SERVICE',7);

//#######################################################
//Workflow Component Types
define('EVENT',1);
define('PROCESS',2);
define('DECISION',3);
define('GENERATOR',4);
define('RULE',5);
define('SENSOR',6);
define('PROGRAM',7);
define('INPUT',8);
define('SERVICE',9);
define('NOTIFICATION',10);
define('REPORT',11);
define('AUTHORIZATION',12);
