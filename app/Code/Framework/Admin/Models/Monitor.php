<?php
namespace Code\Framework\Admin\Models;
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
    
    public function snapshot() {
        $this->system = Humble::helper('admin/system');
        $memory = $this->system->serverMemoryUsage();
        print_r($memory);
        $this->setMemoryFree($memory['free'] ?? '0');
        $this->setMemoryTotal($memory['total'] ?? '0');
        $this->setMemoryUsed($memory['used'] ?? '0');
        $this->setMemoryPercentage($memory['percent'] ?? '0');
        $this->setThreadCount($this->system->threadCount('apache2'));
        $this->setTaskCount($this->system->taskCount());
        $this->setServerLoad($this->system->serverLoad());
        
    }
}