<?php


/*  ____                                      
   / __ \_________  ____ __________ _____ ___ 
  / /_/ / ___/ __ \/ __ `/ ___/ __ `/ __ `__ \
 / ____/ /  / /_/ / /_/ / /  / /_/ / / / / / /
/_/   /_/__ \____/\__, /_/   \__,_/_/ /_/ /_/ 
| |     / /______/____/__  ____  ___  _____   
| | /| / / ___/ __ `/ __ \/ __ \/ _ \/ ___/   
| |/ |/ / /  / /_/ / /_/ / /_/ /  __/ /       
|__/|__/_/   \__,_/ .___/ .___/\___/_/        
                 /_/   /_/                    
 
 We are going to get a JSON object with program information,
 record the PID, and then run the program outlined
 */

$data = $argv;
ob_start();
if ($args = $argv[1] ?? false) {
    $cmds = json_decode($args,true);
    print_r($cmds);
    $root = $cmds['root']       ?? ".";
    $pgm  = $cmds['program']    ?? "unknown";
    $ns   = $cmds['namespace']  ?? "unknown";
    if ($cmd  = $cmds['command']    ?? false) {
        $pid_file = 'PIDS/'.$pgm.'_'.$ns.'.pid';
        file_put_contents($pid_file,getmypid());
        if (chdir($root)) {
            //$results = shell_exec($cmd);
            echo `{$cmd}`;
            //exec($cmd,$results,$rc);
            //print_r($results);
        } else {
            print("Did not change directory\n");
        }
        if (file_exists($pid_file)) {
            unlink($pid_file);
        }
    }
}
print('Finished'."\n");
file_put_contents('runwrapper.txt',ob_get_flush());


