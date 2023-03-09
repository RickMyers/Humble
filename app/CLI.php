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
//Will dynamically generate help information for a command
//--------------------------------------------------------------------------
function describe($command=false) {
    
}
//--------------------------------------------------------------------------
// These commands are what we can handle... the following array identifies
//  the include that contains the command code.  These includes can be found
//  in the '/app/cli' directory
//--------------------------------------------------------------------------
$available_commands = [
    'Workflow' => [
        'w' => 'Examines and registers workflow components in a modules models',
        'z' => 'Generate Workflows'
    ],
    'Framework' => [
        'x' => 'Check if a module prefix is available (deprecated)',
        'a' => 'Remove AUTOINCREMENT=# from SQL dumps',
        'c' => 'Check for namespace availability',
        'p' => 'Preserve a directory',
        'r' => 'Restore a directory'
    ],
    'System' => [
        'o' => 'Toggles the application online or offline',
        'l' => 'Toggle local authentication vs SSO',
        's' => 'Application Status'
    ],
    'Module' => [
        'b' => 'Build a new module',
        'i' => 'Install a module',
        'u' => 'Update a module',
        'e' => 'Enable a module',
        'd' => 'Disable a module',
        'k' => 'Uninstall (Kill) a module'        
    ],
    'Component' => [
        'cc' => 'Creates a Controller',
        'cm' => 'Creates a Component (Model, Helper, Entity)',
        'y'  => 'Compiles a Controller',
        'g'  => 'Generate JSON Edits (Not-Implemented)'
    ],
    'Directives' => [
        'activate' => 'Build, Install, and Enable a Module',
        'use' => 'Update a module using the relative location of a configuration file',
        'adduser' => 'Create a user in the humble user directory',
        'package' => 'Creates a new downloadable archive file of the framework',
        'increment' => 'Increments the minor version by 1, rolling over if needed',
        'initialize' => 'Initializes a new project based on the Humble Framework',
        'export' => 'Exports workflows to a pre-defined server/environment',
        'patch' => 'Updates teh Humble Base Framework files with any new updates, respecting manifested files',
        'sync' => 'Updates the core files'
    ]
];
$args = [];                                                                     //declaring global variable
if ((array_shift($argv)) && ($entered_command = parseCommand($argv))) {         //pop program name and grab the command they entered
    foreach ($available_commands as $include => $commands) {                    //go find which include we should bring in (functionality)
        foreach ($commands as $command => $description) {
            if ($entered_command===$command) {
                array_shift($argv);                                             //drop the entered command
                foreach ($argv as $arg) {
                    $args[] = $arg;                                             //we are copying passed in args to a global variable
                }
                require_once 'cli/'.$include.'.php';                            //bring in the custom configuratino for this command
                if (strtolower($args[0]) === 'help') {                          //php CLI.php --u help   #handles a request for information on a command
                    describe($command);
                } else {
                    processCommand($command,$description);                      //ok, lets go handle the command using the custom config included above
                }
                break;                                                          //we found our command, no need for more work
            }      
        }

    }
} else {
    print('No command passed to execute, aborting.'."\n");
}