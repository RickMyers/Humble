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

if (!class_exists('Humble')) {
    //let's make sure we only include/define these once
    require_once('Humble.php');
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
// Prints the full list of commands we can handle at the command line
//--------------------------------------------------------------------------
function printHelp($available_commands=[]) {
    print("\n".'Help is available for the following commands:'."\n");
    foreach ($available_commands as $include => $commands) {
        print("\nTopic: ".$include."\n");
        foreach ($commands as $command => $options) {
            $command = str_replace('|',' or ',$command);
            print("\t--".$command.' - '.($options['description'] ?? 'N/A')."\n");
        }
    }
    print("\nFor detailed help, type 'humble --command help'\n\n");
}
//--------------------------------------------------------------------------
// Prints the full list of commands we can handle at the command line
//--------------------------------------------------------------------------
function printTopicHelp($topic,$commands=[]) {
    print("\n".$topic.' help is available for the following commands:'."\n\n");
    foreach ($commands[$topic] as $command => $options) {
        $command = str_replace('|',' or ',$command);
        print("\t--".$command.' - '.($options['description'] ?? 'N/A')."\n");
    }
    print("\nFor detailed help, type 'humble --command help'\n\n");
}
//--------------------------------------------------------------------------
// Iterates through directories accumulating the various commands
//--------------------------------------------------------------------------
function aggregateDirectories($dh) {
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
    return $available_commands;
}
//--------------------------------------------------------------------------
// Iterates through available modules accumulating the various commands
//--------------------------------------------------------------------------
function aggregateModuleCommands() {
    $available_commands = [];        
    foreach ($modules = \Humble::entity('humble/modules')->setEnabled('Y')->setCli('Y')->fetch() as $module) {
        $commands = 'Code/'.$module['package'].'/'.$module['module'].'/cli/directory.yaml';
        if (file_exists($commands)) {
            $available_commands[ucfirst($module['namespace'])] = yaml_parse_file($commands);
        }
    }
    return $available_commands;
}
function modulePath($namespace=false) {
    $path = '';
    if ($module = \Humble::entity('humble/modules')->setNamespace(strtolower($namespace))->load(true)) {
        $path = 'Code/'.$module['package'].'/'.$module['module'].'/cli/'.$namespace.'.php';
    }
    return $path;
}
//--------------------------------------------------------------------------
// Due to different ways we can request help and argument formats...
//  necessitates this monstrosity
//--------------------------------------------------------------------------
function helpRequest($first_parm,$details) {
    global $help_cmd;
    return (($details['directive'] ?? false )===true) ? (isset($help_cmd[strtolower($first_parm)])) : ((!$first_parm) || (isset($help_cmd[strtolower($first_parm)])));  //Yikes! Now I need a shower...
}

//==========================================================================

$help_cmd = ['help'=>true,'?'=>true];
if (!count($argv ?? []) && (count($args ?? []))) {
    $argv = $args;  //not called from command line but included by another program so we are faking it
}
$args               = [];                                                       //declaring global variable
$available_commands = array_merge_recursive(aggregateDirectories(dir('cli')),aggregateModuleCommands());   
if ((array_shift($argv)) && ($entered_command = parseCommand($argv))) {         //pop program name and grab the command they entered
    if (isset($help_cmd[strtolower($entered_command)])) {
        if (isset($argv[1])) {
            printTopicHelp(ucfirst(strtolower($argv[1])),$available_commands);
        } else {
            printHelp($available_commands);
        }
    } else {
        foreach ($available_commands as $include => $commands) {                //go find which include we should bring in (functionality)
            foreach ($commands as $command => $options) {
                $list = [];
                foreach (explode('|',$command) as $cmd) {                       //we are going to allow for command aliases specified in the yaml file by pipe delimiters in the command [command|cmd|c]
                    $list[$cmd] = true;
                } 
                if (isset($list[$entered_command])) {
                    array_shift($argv);                                         //drop the entered command
                    foreach ($argv as $arg) {                                   //Pre-process the passed in arguments, filtering if a ' is found
                        if (strpos($arg,"'")) {
                            die("Single quote detected in argument, use double quotes instead\n");
                        }
                        $args[] = $arg;                                             //we are copying passed in args to a global variable
                    }
                    $files = ['classes'   => (($options['custom'] ?? false) ? modulePath($include) :'cli/'.$include.'/'.$include.'.php')];
                    foreach ($files as $type => $file) {
                        if (file_exists($file)) {
                            require_once $file;
                        }
                    }
                    if (helpRequest(($argv[0] ?? ''),$options)) {         //php CLI.php --u help   #handles a request for information on a command
                        $include::describe($command,$options);
                    } else {
                        if (isset($options['function']) && $options['function']) {
                            $method = $options['function'];
                            $include::arguments($include::verifyArguments($args,$options));
                            if ($returned = $include::$method()) {
                                (is_string($returned)) ? print("\n".$returned."\n\n") : print_r($returned);
                            }
                        }
                    }
                    break;                                                          //we found our command, no need for more work
                }      
            }
        }
    }
} else {
    print('No command passed to execute, aborting.'."\n");
}