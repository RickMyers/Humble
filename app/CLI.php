<?php
/*
 * 
 *     __  __                __    __        ________    ____
 *    / / / /_  ______ ___  / /_  / /__     / ____/ /   /  _/
 *   / /_/ / / / / __ `__ \/ __ \/ / _ \   / /   / /    / /  
 *  / __  / /_/ / / / / / / /_/ / /  __/  / /___/ /____/ /   
 * /_/ /_/\__,_/_/ /_/ /_/_.___/_/\___/   \____/_____/___/   
 *                                                         
 *  You can do a lot from here...
 * 
 */
//print('Working on it...'."\n");
//ob_start();
if (!class_exists('Humble')) {
    //let's make sure we only include/define these once
    require_once('Humble.php');
}
//--------------------------------------------------------------------------
// Name says it all... acquired from PHPPro blog
//--------------------------------------------------------------------------
function underscoreToCamelCase($string,$first_char_caps=false) {
    return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
}
//--------------------------------------------------------------------------
// Tries to retrieve what command you are trying to get processed
//--------------------------------------------------------------------------
function parseCommand($args=[]) {
    $command = null;
    if (count($args)) {
        $command = ($commands = explode('-',$args[0]))[count($commands)-1];     //one dash, two dash, three dash... I don't give a F...
    }
    return strtolower($command);
}
//--------------------------------------------------------------------------
$dh                 = dir('cli');
$available_commands = [];
while ($entry = $dh->read()) {
    if (($entry == '.') || ($entry == '..')) {
        continue;
    }
    if (is_dir('cli/'.$entry)) {
        if (file_exists('cli/'.$entry.'/directory.yaml')) {
            $available_commands[$entry] = yaml_parse_file('cli/'.$entry.'/directory.yaml');
        }
    }
}

$args = [];                                                                     //declaring global variable
if ((array_shift($argv)) && ($entered_command = parseCommand($argv))) {         //pop program name and grab the command they entered
    foreach ($available_commands as $include => $commands) {                    //go find which include we should bring in (functionality)
        foreach ($commands as $command => $options) {
            if ($entered_command===$command) {
                array_shift($argv);                                             //drop the entered command
                foreach ($argv as $arg) {
                    if (strpos($arg,"'")) {
                        die("Single quote detected in argument, use double quotes instead\n");
                    }
                    $args[] = $arg;                                             //we are copying passed in args to a global variable
                }
                $files = [
                    'control'   => 'cli/'.$include.'/control.php',
                    'classes'   => 'cli/'.$include.'/'.$include.'.php'
                ];
                //print_r($options);

                foreach ($files as $type => $file) {
                    if (file_exists($file)) {
                        require_once $file;
                    }
                }
                if (strtolower($args[0] ?? 'help') === 'help') {                          //php CLI.php --u help   #handles a request for information on a command
                    $include::describe($command,$options);
                } else {
                    if (isset($options['function']) && $options['function']) {
                        $method = $options['function'];
                        $include::arguments($include::verifyArguments($args,$options));
                        $include::$method();
                    }
                }
                break;                                                          //we found our command, no need for more work
            }      
        }

    }
} else {
    print('No command passed to execute, aborting.'."\n");
}