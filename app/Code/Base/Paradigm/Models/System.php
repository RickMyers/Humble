<?php
namespace Code\Base\Paradigm\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * System Events
 *
 * Time based system event methods (think CRON)
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Richard Myers <rick@humblecoding.com>
 * @since      File available since Version 1.0.1
 */
class System extends Model
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return system
     */
    public function getClassName() {
        return __CLASS__;
    }

    /**
     * Fetches all templates used by decoupled engines
     *
     * @return array
     */
    public function templates() {
        $templates = [];
        $dir = Humble::getHelper('humble/directory');
        foreach (Humble::getEntity('humble/modules')->setEnabled('Y')->fetch() as $module) {
            $templates[$module['namespace']] = [];
            if (is_dir('Code/'.$module['package'].'/'.$module['module'].'/web/app')) {
                $d = $dir->contents('Code/'.$module['package'].'/'.$module['module'].'/web/app',true);
                foreach ($dir->contents('Code/'.$module['package'].'/'.$module['module'].'/web/app',true) as $file) {
                    if (!is_dir($file)) {
                        $parts = explode('/',$file);
                        $template = $parts[count($parts)-1];
                        $template = explode('.',$template);
                        $templates[$module['namespace']][$template[0]] = addslashes(file_get_contents($file));
                    }
                }
            }
        }
        return json_encode($templates);
    }
    /**
     * We use the normal component save here, and then register the integration point
     *
     */
    public function save() {
        $data           = json_decode($this->getData(),true);
        $component      = Humble::getModel('workflow/manager');
        $component->setData($this->getData());
        $component->saveComponent();
        $this->setWindowId($data['window_id']); //passing on the window id so we can auto close the window
        $system_event = \Humble::getEntity('paradigm/system_events');
        $system_event->setWorkflowId($data['workflow_id']);
        $system_event->setEventStart($data['event_date'].' '.$data['event_time']);
        $system_event->setRecurring($data['recurring_flag']);
        $system_event->setPeriod($data['period']);
        $system_event->setActive($data['active']);
        $system_event->save();

    }

    /**
     * This method will fetch all active system events and then depending on the period and frequency of execution, will queue the event for execution if we are within 5 minutes of its next execution window
     *
     * @return boolean
     */
    public function runScheduler() {
        //@TODO: Think about setting a sticky bit that flags the scheduler as running, so we don't launch this thing more than once
        $now             = strtotime(date('Y-m-d H:i:s'));
        $job_queue       = Humble::getEntity('paradigm/job_queue');
        $schedule_log    = Humble::getEntity('paradigm/scheduler_log')->setStarted(date('Y-m-d H:i:s'));    //Let's record when you started
        $schedule_id     = $schedule_log->save();   
        $jobs            = Humble::getEntity('paradigm/system_events')->setActive('Y')->fetch();//And persist it
        foreach ($jobs as $event) {
            //if your next execution cycle is within 5 minutes and you haven't been run in the last 10 minutes, you will be queued for execution
            if ((int)$event['period'] == $event['period']) {
                if ((!$event['last_run']) || ($now - strtotime($event['last_run']) >= 600)) {
                    $int = ($event['period'] - ($now - strtotime($event['event_start'])) % $event['period']);
                    if ($int <= 300) {
                        $queued = $job_queue->reset()->setSystemEventId($event['id'])->setStatus(NEW_EVENT_JOB)->load(true);
                        if (!$queued) {
                            //Don't queue it up if there's one run there already
                            $job_queue->reset()->setSystemEventId($event['id'])->setQueued(date('Y-m-d H:i:s',$now))->save();
                        }
                    }
                }
            }
        }
        $schedule_log->reset()->setId($schedule_id)->setFinished(date('Y-m-d H:i:s'))->save();                                            //Save when the scheduler finished, this is also and audit trail since if there are no values for finished... it didn't for some reason
        return true;
    }

    /**
     * This method will go through the job queue table and launch any jobs in there that have
     *
     * @return boolean
     */
    public function runLauncher() {
        $queue  = Humble::getEntity('paradigm/job_queue');
        $jobs   = $queue->setStatus(NEW_EVENT_JOB)->fetch();
        foreach ($jobs as $job) {
            //$cmd = 'php launch.php '.$job['id']." > ../SDSF/job_".$job['id'].".txt 2>&1";
            $cmd = 'php launch.php '.$job['id'];
            print("Running launcher at ".date("H:i:s")."\n");
            print($cmd."\n");
            if ($this->_isWindows) {
                pclose(popen("start ".$cmd,"r"));
            } else {
                exec('/usr/bin/nohup '.$cmd.' 2>&1 &');
            }
            print("Done at ".date("H:i:s")."\n");

        }
        return true;
    }

}

