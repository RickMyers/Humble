<?php
namespace Code\Base\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * System Monitor
 *
 * Collects system information and saves a snapshot of the data so we can
 * track performance over time
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Monitor extends Model
{

    private $system = null;
    
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
     "cpu": {
        "percentage": "{$monitor}"
    },
    "memory": {
        "usage": "{$monitor}",
        "percentage": "{$monitor}"    
    },
    "apache": {
        "thread_count": "{$monitor}"
    },
    "tasks": {
        "count": "{$monitor}"
    },
    "load": {
        "average": "{$monitor}"
    },
    "uptime": {
        "duration": "{$monitor}"
    }
 */
    public function snapshot() {
        print("Generating Snapshot\n");
        $this->system = Humble::helper('humble/system');
        if ($memory = $this->system->serverMemoryUsage()) {
            $this->setMemoryUsage($memory[1] ?? '0');
            $this->setMemoryTotal($memory[0] ?? '0');
            $this->setMemoryPercentage($memory[2] ?? '0%');
        }
        print_r($memory);
        print($this->getThreadCount()."\n");
        $this->setThreadCount($this->system->threadCount('apache2'));
        $this->setTaskCount($this->system->taskCount());
        print_r($this->system->serverLoad());
        //$this->setLoadAverage();
        //$this->setUptimeDuration();
        
    }
}