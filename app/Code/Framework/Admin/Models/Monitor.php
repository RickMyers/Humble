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
        $this->setServerLoad($this->system->serverCpuUsage());
        return [
            'free_memory'       => $this->getFreeMemory(),
            'total_memory'      => $this->getTotalMemory(),
            'used_memory'       => $this->getUsedMemory(),
            'percent_memory'    => $this->getPercentMemory(),
            'apache_threads'    => $this->getApacheThreads(),
            'fpm_threads'       => $this->getFPMThreads(),
            'server_load'       => $this->getServerLoad(),
            'total_threads'     => $this->getTotalThreads(),
            'cpu_usage'         => $this->getCpuUsage()
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
            $monitor->setCpu($stats['cpu_usage']);
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
    
    private function scrunchResults($results=[]) {
        if (count($results)) {
            for ($i=0; $i<count($results); $i++) {
                $row   = [];
                $parts = explode(' ',$results[$i]);
                foreach ($parts as $part) {
                    if (($p = trim($part)) || ($p!=='')) {
                        $row[] = $p;
                    }
                }
                //$results[$i] = implode('|',$row);
                $results[$i] = $row;
            }
        }
        return $results;
    }
    
    private function parseProcesses($data=[]) {
        $processes = [];
        for ($i=7; $i<count($data); $i++) {
            $processes[] = [
                "PID"       => $data[$i][0],
                "owner"     => $data[$i][1],
                "priority"  => $data[$i][2],
                "nice"      => $data[$i][3],
                "mem_vir"   => $data[$i][4],
                "mem_res"   => $data[$i][5],
                "mem_shr"   => $data[$i][6],
                "status"    => $data[$i][7],
                "cpu_prc"   => $data[$i][8],
                "mem_prc"   => $data[$i][9],
                "time"      => $data[$i][10],
                "command"   => $data[$i][11],
            ];
        }
        return $processes;
    }
    private function parseStatus($data=[]) {
        //file_put_contents('top.txt',print_r($data,true));
        $status = [];
        $status['time']     = $data[0][2];
        $status['uptime']   = $data[0][4].' '.$data[0][5];
        $status['users']    = $data[0][7];
        $status['load']     = $data[0][9].' '.($data[0][10]??'').' '.($data[0][11]??'');
        $status['tasks']    = $data[1][1];
        $status['running']  = $data[1][3];
        $status['sleeping'] = $data[1][5];
        $status['stopped']  = $data[1][7];
        $status['zombie']   = $data[1][9];
        $status['cpu_us']   = $data[2][1]; /* user space */
        $status['cpu_sy']   = $data[2][3]; /* kernel space */
        $parts = explode(',',$data[2][6]);
        $status['cpu_id']   = $parts[1];   /* idle */
        $status['cpu_wa']   = $data[2][8];
        $status['cpu_hi']   = $data[2][10]; /* hardware interrupts */
        $status['cpu_si']   = $data[2][12]; /* software interrupts */
        $status['cpu_vm']   = $data[2][14]; /* lost to virtual machines */
        $status['mem_tot']  = $data[3][3];
        $status['mem_fre']  = $data[3][5];
        $status['mem_use']  = $data[3][7];
        $status['mem_buf']  = $data[3][9];
        $status['swap_tot'] = $data[4][2];
        $status['swap_fre'] = $data[4][4];
        $status['swap_use'] = $data[4][6];
        $status['swap_av']  = $data[4][8];
        return $status;
    }
    /**
     * Will take the output from TOP and serialize it into JSON breaking it into two sections, a system section and a processes section
     * 
     * @return json
     */
    public function systemStatus() {
        exec('top -n 1 -b',$results,$rc);
        //exec('ps -aux',$processes,$rc);  //someday xref these 2
        $results = $this->scrunchResults($results);        
        return json_encode($status = [
            'system'    =>$this->parseStatus($results),
            'processes' =>$this->parseProcesses($results)
        ],JSON_PRETTY_PRINT);
    }
}