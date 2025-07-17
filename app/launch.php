<?php
/**
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
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
$project = \Environment::project();
if (file_exists($custom = 'Code/'.$project->package.'/'.$project->module.'/includes/Custom.php')) {
    require $custom;
}
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
$job        = Humble::entity('paradigm/job/queue')->setId($job_id);
$job->setId($job_id)->setStarted(date('Y-m-d H:i:s'))->setPid($pid)->save();    //persist the PID of this run
$job->reset()->setId($job_id)->load();                                          //reload the job
$event      = Humble::entity('paradigm/system/events');
$event_data = $event->setId($job->getSystemEventId())->load();
$event->reset()->setId($event_data['id'])->setLastRun(\date('Y-m-d H:i:s'))->save();
$workflow   = ($event_data && isset($event_data['workflow_id']) && $event_data['workflow_id']) ? $event_data['workflow_id'] : false;
if ($workflow) {
    if (file_exists('Workflows/'.$workflow.".php")) {
        print('Executing Workflow: '.$workflow."\n");
        if ($event_data = Humble::entity('paradigm/workflows')->setWorkflowId($workflow)->load(true)) {
            $data['title']       = $event_data['title'];
            $data['description'] = $event_data['description'];
        }
        $EVENT = Event::get('SystemEvent',$data);
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
print("Launcher finished at ".date("H:i:s")."\n");
file_put_contents('../SDSF/job_'.$job_id.'.txt',ob_get_clean());
$job->setFinished(date('Y-m-d H:i:s'))->save();
exit($rc);