<?php
/* #######################################################################################################
                    _   _                _          _   _               ______             _
         /\        | | | |              (_)        | | (_)             |  ____|           (_)
        /  \  _   _| |_| |__   ___  _ __ _ ______ _| |_ _  ___  _ __   | |__   _ __   __ _ _ _ __   ___
       / /\ \| | | | __| '_ \ / _ \| '__| |_  / _` | __| |/ _ \| '_ \  |  __| | '_ \ / _` | | '_ \ / _ \
      / ____ \ |_| | |_| | | | (_) | |  | |/ / (_| | |_| | (_) | | | | | |____| | | | (_| | | | | |  __/
     /_/    \_\__,_|\__|_| |_|\___/|_|  |_/___\__,_|\__|_|\___/|_| |_| |______|_| |_|\__, |_|_| |_|\___|
                                                                                      __/ |
                                                                                     |___/
   #########################################################################################################

    How it works:
        o Attributes on the controller identify whether the service is eligible for authorization
        o We have access to the namespace, controller and service from the front-controller
        o We use ns/cn/sv to look up what the relationship is
        o We consult the "ns/relationships" table to see if a relationship exists for that type
        o This really isn't implemented... but was a nice thought
*/
/*
try {
    $engine = \Humble::entity('humble/service/directory')->setNamespace($namespace)->setRouter($controller)->setService($method)->load(true);
    if (isset($engine['authorized']) && ($engine['authorized']==='Y'))  {
        //do the authorization check, how we do this is a TBD
        $authorized = true;
        switch ($authorized) {
            case true   :   //nop
                            break;
            case false  :   throw new AuthorizationException('You lack the required authority to perform that action',16);
                            break;
            default     :   throw new AuthorizationException('An error occurred in the authorization engine',20);
                            break;
        }
    }
} catch (AuthorizationException $ex) {
    Environment::standard($ex,'Authorization Error','authorization');
    die();
}
 
 */
?>