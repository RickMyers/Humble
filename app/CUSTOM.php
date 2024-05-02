<?php
/* -----------------------------------------------------------------------------
 * We are going to be looking for attempts at cross site request forgeries so
 * we are going to be leaving some data to be sent to the server with each 
 * request made, however, since the user might have the site opened in multiple
 * tabs, we are going to have to keep track of the data per tab, since the forms
 * may be open in multiple tab windows.  This step initializes an array to begin
 * storing tab tokens in
 * -----------------------------------------------------------------------------*/
if (!isset($_SESSION['BROWSER_TABS'])) {
    $_SESSION['BROWSER_TABS'] = [];
}

//Overrides---------------------------------------------------------------------
$bypass                     = false;                                            //If you set to true will skip the log in check and public_routes.json lookup.  BE VERY CAREFUL ABOUT SETTING TO TRUE!
$authorizationEngineEnabled = false;                                            //Are we using service level authorizations?  You will need to provide the logic for how that is done
$use_connection_pool        = true;                                             //Set to true to speed up database interaction 
$use_redis                  = false;                                            //Do you want to use REDIS instead of Memcached?
$use_pgsql                  = false;                                            //Do you want to user PostgreSQL instead of MySQL?