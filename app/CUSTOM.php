<?php
/* -----------------------------------------------------------------------------
 * We are going to be looking for attempts at cross site request forgeries so
 * we are going to be leaving some data to be sent to the server with each 
 * request made, however, since the user might have the site opened in multiple
 * tabs, we are going to have to keep track of the data per tab, since the forms
 * may be open in multiple tab windows.  This step initializes an array to begin
 * storing tab tokens in
 * -----------------------------------------------------------------------------
 */
if (!isset($_SESSION['BROWSER_TABS'])) {
    $_SESSION['BROWSER_TABS'] = [];
}

//Overrides
$authorizationEngineEnabled = false;
$use_connection_pool        = true;
$use_redis                  = false;
$use_pgsql                  = false;