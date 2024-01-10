<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * System Monitor
 *
 * Collects/removes system information and saves a snapshot of the data so we can
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
     * Returns 
     * 
     * @return array
     */
    public function snapshot() {
        $this->system = Humble::helper('admin/system');
        $memory = $this->system->serverMemoryUsage();
        $this->setFreeMemory($memory['free']        ?? '0');
        $this->setTotalMemory($memory['total']      ?? '0');
        $this->setUsedMemory($memory['used']        ?? '0');
        $this->setPercentMemory($memory['percent']  ?? '0');
        $this->setApacheThreads($this->system->threadCount('apache2'));
        $this->setFPMThreads($this->system->threadCount('php-fpm'));
        $this->setTotalThreads($this->system->taskCount());
        $this->setServerLoad($this->system->serverLoad());
        return [
            'free_memory'       => $this->getFreeMemory(),
            'total_memory'      => $this->getTotalMemory(),
            'used_memory'       => $this->getUsedMemory(),
            'percent_memory'    => $this->getPercentMemory(),
            'apache_threads'    => $this->getApacheThreads(),
            'fpm_threads'       => $this->getFPMThreads(),
            'server_load'       => $this->getServerLoad(),
            'total_threads'     => $this->getTotalThreads()
        ];
    }
    
    /**
     * Writes system stats to a table
     * 
     * @return $this
     */
    public function record() {
        if ($stats = $this->snapshot()) {
            $monitor = Humble::entity('admin/system/monitor');
            $monitor->setServerLoad($stats['server_load']       ?? 0);
            $monitor->setUtilization($stats['percent_memory']   ?? 0);
            $monitor->setTotalThreads($stats['total_threads']   ?? 0);
            $monitor->setFpmThreads($stats['fpm_threads']       ?? 0);
            $monitor->setApacheThreads($stats['apache_threads'] ?? 0);
            $monitor->save();
        }
        return $this;
    }
    
    /**
     * Removes stats from the database that are over 2 weeks old
     * 
     * @return $this
     */
    public function clear() {
        $two_weeks = 60 * 60 * 24 * 14;
        Humble::entity('admin/system/monitor')->condition("modified < '".date('Y-m-d H:i:s',time() - $two_weeks)."'")->delete(true);
        //print(date('Y-m-d H:i:s',time() - $two_weeks));
        return $this;
    }
}