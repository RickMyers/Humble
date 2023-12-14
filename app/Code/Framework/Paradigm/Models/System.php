<?php
namespace Code\Framework\Paradigm\Models;
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
 * @author     Richard Myers <rick@humbleprogramming.com>
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
        $dir = Humble::helper('humble/directory');
        foreach (Humble::entity('humble/modules')->setEnabled('Y')->fetch() as $module) {
            $templates[$module['namespace']] = [];
            if (is_dir('Code/'.$module['package'].'/'.$module['module'].'/web/app')) {
                $d = $dir->contents('Code/'.$module['package'].'/'.$module['module'].'/web/app',true);
                foreach ($dir->contents('Code/'.$module['package'].'/'.$module['module'].'/web/app',true) as $file) {
                    if (!is_dir($file)) {
                        $parts = explode('/',$file);
                        $template = $parts[count($parts)-1];
                        $template = explode('.',$template);
                        $templates[$module['namespace']][$template[0]] = str_replace(['\r','\n'],['',''],file_get_contents($file));
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
        $component      = Humble::model('workflow/manager');
        $component->setData($this->getData());
        $component->saveComponent();
        $this->setWindowId($data['window_id']); //passing on the window id so we can auto close the window
        $system_event = \Humble::entity('paradigm/system_events');
        $system_event->setWorkflowId($data['workflow_id']);
        $system_event->setEventStart($data['event_date'].' '.$data['event_time']);
        $system_event->setRecurring($data['recurring_flag']);
        $system_event->setPeriod($data['period']);
        $system_event->setActive($data['active_flag']);
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
        $job_queue       = Humble::entity('paradigm/job/queue');
        $schedule_log    = Humble::entity('paradigm/scheduler/log');   
        $schedule_id     = $schedule_log->setStarted(\date('Y-m-d H:i:s'))->save();    //Let's record when you started
        foreach (Humble::entity('paradigm/system/events')->setActive('Y')->fetch() as $event) {
            //if your next execution cycle is within 5 minutes and you haven't been run in the last 10 minutes, you will be queued for execution
            if ((int)$event['period'] == $event['period']) {
                if ((!$event['last_run']) || ($now - strtotime($event['last_run']) >= 600)) {
                    $med = $now - strtotime($event['event_start']);             //This is the time since the event was initially run
                    $off = ($med % $event['period']);                           //This is the remainder if you divide that time by the event period
                    $int = ($event['period'] - $off) ;                          //And this subtracts the value to see if we are almost at the period where we need to run it again
                    if (($int <= 300) || ($med < (int)$event['period'])) {      //If the next interval is within 5 minutes 
                        $queued = $job_queue->reset()->setSystemEventId($event['id'])->setStatus(NEW_EVENT_JOB)->load(true);
                        if (!$queued) {
                            //Don't queue it up if there's one run there already
                            $job_queue->reset()->setSystemEventId($event['id'])->setQueued(date('Y-m-d H:i:s',$now))->save();
                        }
                    }
                }
            }
        }
        return $schedule_log->reset()->setId($schedule_id)->setFinished(date('Y-m-d H:i:s'))->save();    //Save when the scheduler finished, this is also an audit trail since if there are no values for finished... it didn't for some reason work
    }

    /**
     * This method will go through the job queue table and launch any jobs in there that have
     *
     * @return boolean
     */
    public function runLauncher() {
        $queue  = Humble::entity('paradigm/job/queue');
        $jobs   = $queue->setStatus(NEW_EVENT_JOB)->fetch();
        foreach ($jobs as $job) {
            //$cmd = 'php launch.php '.$job['id']." > ../SDSF/job_".$job['id'].".txt 2>&1";
            $cmd = Environment::PHPLocation().' launch.php '.$job['id'].' 2>&1';
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

