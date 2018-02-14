<?php
/**     ______               _      
       |  ____|             | |     
       | |____   _____ _ __ | |_    
       |  __\ \ / / _ \ '_ \| __|   
       | |___\ V /  __/ | | | |_    
  _____|______\_/ \___|_| |_|\__|   
 |__   __| (_)                      
    | |_ __ _  __ _  __ _  ___ _ __ 
    | | '__| |/ _` |/ _` |/ _ \ '__|
    | | |  | | (_| | (_| |  __/ |   
    |_|_|  |_|\__, |\__, |\___|_|   
               __/ | __/ |          
              |___/ |___/    
 * 
 * A command line wrapper for triggering events      
 */

require "Argus.php";

$program    = array_shift($argv);
$event_name = array_shift($argv);
$data       = [];
foreach ($argv as $arg) {
    $d = explode('=',$arg);
    $data[$d[0]] = $d[1];
}
$event = Event::trigger(Event::get($event_name,$data));

//fin