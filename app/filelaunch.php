<?php
/**
 * File...
 __    __           _     __ _
/ / /\ \ \___  _ __| | __/ _| | _____      __
\ \/  \/ / _ \| '__| |/ / |_| |/ _ \ \ /\ / /
 \  /\  / (_) | |  |   <|  _| | (_) \ V  V /
  \/  \/ \___/|_|  |_|\_\_| |_|\___/ \_/\_/
   __                        _
  / /  __ _ _   _ _ __   ___| |__   ___ _ __
 / /  / _` | | | | '_ \ / __| '_ \ / _ \ '__|
/ /__| (_| | |_| | | | | (__| | | |  __/ |
\____/\__,_|\__,_|_| |_|\___|_| |_|\___|_|

 *
 */

require "Humble.php";
require "Code/Framework/Humble/includes/Constants.php";
ob_start();
print("Launcher fired at ".date("H:i:s")."\n");
$rc         = 0;
$args       =  array_slice($argv,1);
$data       = [];
$job_id     = ($args[0]) ? $args[0] : false;
if (isset($args[1])) {
    foreach (array_slice($args,1) as $arg) {
        if (strpos($arg,'=')) {
            $d  = explode('=',$arg);
            $data[$d[0]] = $d[1];
        } else {
            $data[] = $arg;
        }
    }
}
$pid        = getmypid();
$job        = Humble::entity('paradigm/job/queue');
$job->setId($job_id)->setStarted(date('Y-m-d H:i:s'))->setPid($pid)->save();    //persist the PID of this run
$job->reset()->setId($job_id)->load();                                          //reload the job
$workflow_data  = Humble::entity('paradigm/workflows')->setId($job->getWorkflowId())->load();
$workflow       = ($workflow_data && isset($workflow_data['workflow_id']) && $workflow_data['workflow_id']) ? $workflow_data['workflow_id'] : false;
$filename       = $job->getFilename();
$action         = $job->getFileAction() ?? 'new';
if ($workflow && $job->getFilename()) {
    if (file_exists('Workflows/'.$workflow.".php")) {
        print('Executing Workflow: '.$workflow."\n");
        $parts  = explode('/',$filename); //this is messy... do this better with stacks and queues
        $file   = $parts[count($parts)-1];
        unset($parts[count($parts)-1]);
        $dir    = implode('/',$parts);
        $EVENT  = Event::get($action.'FileEvent',['type'=>$action,'path'=>$filename,'dir'=>$dir,'name'=>$file,'job_id'=>$job_id]);
        include('Workflows/'.$workflow.".php");
        $job->setStatus(JOB_COMPLETED);
        $job->setComment('Ok');
    } else {
        print('Missing Workflow: '.$workflow."\n");
        $job->setComment("The workflow [".$workflow."] does not exist.  Maybe you need to generate it?");
        $job->setStatus(JOB_FAILED);
        $rc = 12;
    }
} else {
    print('Could not find the system event containing the workflow for job: '.$job_id);
    $job->setComment('Could not find the system event containing the workflow for job: '.$job_id);
    $job->setStatus(JOB_FAILED);
    $rc = 8;
}

file_put_contents('../SDSF/job_'.$job_id.'.txt',ob_get_clean());
$job->setFinished(date('Y-m-d H:i:s'))->save();
exit($rc);
