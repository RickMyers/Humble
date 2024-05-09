<?php
/**
    ____                                          __ 
   /  _/___  _____________  ____ ___  ___  ____  / /_
   / // __ \/ ___/ ___/ _ \/ __ `__ \/ _ \/ __ \/ __/
 _/ // / / / /__/ /  /  __/ / / / / /  __/ / / / /_  
/___/_/ /_/\___/_/   \___/_/ /_/ /_/\___/_/ /_/\__/  
        | |  / /__  __________(_)___  ____           
        | | / / _ \/ ___/ ___/ / __ \/ __ \          
        | |/ /  __/ /  (__  ) / /_/ / / / /          
        |___/\___/_/  /____/_/\____/_/ /_/           
                                                     

  A simple program to increment the 5 part version number, used during distribution
 
* PHP version 7.2+
*
* @category   Framework
* @package    Core
* @author     Original Author <rick@humbleprogramming.com>
* @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
* @license    https://humbleprogramming.com/LICENSE.txt
* @version    1.0.1
*/
function incrementVersion($next=1) {
    print("CHANGING VERSION");
    $data  = (file_exists('../etc/application.xml')) ? file_get_contents('../etc/application.xml') : die("Error, application file not found");
    $data  = simplexml_load_string($data);    
    $v     = explode('.',(string)$data->version->framework);
    for ($i=count($v)-1; $i>=0; $i-=1) {                                    //This is one of those ridiculously evil things in computer science
        $v[$i] = (int)$v[$i]+$next;
        if ($next  = ($v[$i]===10)) {
            $v[$i] = 0;
        }
    }
    $data->version->framework = (string)implode('.',$v);
    print("\nSetting version to ".$data->version->framework."\n\n");
    file_put_contents('../etc/application.xml',$data->asXML());
}
incrementVersion();

