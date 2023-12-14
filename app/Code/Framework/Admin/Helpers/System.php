<?php
namespace Code\Framework\Admin\Helpers;
use Humble;
use Log;
use Environment;
/**
 *
 * System Utility Calls
 *
 * Used to check CPU/Memory things, based on someone else's code (not sure who)
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Core
 * @author     Rick Myers rick@humbleprogramming.com
 */
class System extends Helper
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
     * Returns the various load averages
     * 
     * @return array
     */
    protected function serverLoadLinuxData() {
        if ($stats = @file_get_contents("/proc/stat")) {
            $stats = preg_replace("/[[:blank:]]+/", " ", $stats);               // Remove double spaces to make it easier to extract values with explode()
            $stats = str_replace(["\r\n", "\n\r", "\r"], "\n", $stats);         // Separate lines
            $stats = explode("\n", $stats);
            foreach ($stats as $statLine) {                                     // Separate values and find line for main CPU load
                if ((count($statLineData = explode(" ", trim($statLine))) >= 5) && ($statLineData[0] == "cpu")) {
                    return [
                        $statLineData[1],
                        $statLineData[2],
                        $statLineData[3],
                        $statLineData[4],
                    ];
                }
            }
        }
        return null;
    }

    /**
     * Just returns how many total running tasks there are, minus the task list command
     * 
     * @return type
     */
    public function taskCount() {
        @exec('ps -e',$result);
        return count($result)-1;
    }
    /**
     * Counts the number of times 
     * 
     * @param string $target
     * @return type
     */
    public function threadCount($target) {
        if ($result = (int)shell_exec('ps -aux | grep -c "'.$target.'"')) {
            $result--;                                                          //Must remove the observation for running grep
        }
        return $result;
    }
    
    /**
     * Returns server load in percent (just number, without percent sign)
     * @return type
     */
    public function serverLoad() {
        $load = null;
        if (stristr(PHP_OS, "win")) {
            $cmd = "wmic cpu get loadpercentage /all";
            @exec($cmd, $output);
            if ($output) {
                foreach ($output as $line) {
                    if ($line && preg_match("/^[0-9]+\$/", $line)){
                        $load = $line;
                        break;
                    }
                }
            }
        } else {
            if (is_readable("/proc/stat")) {
                $statData1 = $this->serverLoadLinuxData();
                sleep(1);
                $statData2 = $this->serverLoadLinuxData();
                if ((!is_null($statData1)) && (!is_null($statData2))) {
                    $statData2[0] -= $statData1[0];
                    $statData2[1] -= $statData1[1];
                    $statData2[2] -= $statData1[2];
                    $statData2[3] -= $statData1[3];
                    $cpuTime       = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];
                    $load          = 100 - ($statData2[3] * 100 / $cpuTime);
                }
            }
        }
        return $load;
    }

    /**
     * Returns used memory (either in percent (without percent sign) or free and overall in bytes)
     * 
     * @return array
     */
    public function serverMemoryUsage()    {
        $memoryTotal = null;
        $memoryFree  = null;
        if (stristr(PHP_OS, "win")) {
            $cmd = "wmic ComputerSystem get TotalPhysicalMemory";               // Get total physical memory (this is in bytes)
            @exec($cmd, $outputTotalPhysicalMemory);
            $cmd = "wmic OS get FreePhysicalMemory";                            // Get free physical memory (this is in kibibytes!)
            @exec($cmd, $outputFreePhysicalMemory);
            if ($outputTotalPhysicalMemory && $outputFreePhysicalMemory) {
                foreach ($outputTotalPhysicalMemory as $line) {                 // Find total value
                    if ($line && preg_match("/^[0-9]+\$/", $line)) {
                        $memoryTotal = $line;
                        break;
                    }
                }
                foreach ($outputFreePhysicalMemory as $line) {                  // Find free value
                    if ($line && preg_match("/^[0-9]+\$/", $line)) {
                        $memoryFree = $line;
                        $memoryFree *= 1024;                                    // convert from kibibytes to bytes
                        break;
                    }
                }
            }
        } else {
            if (is_readable("/proc/meminfo")) {
                if ($stats = file_get_contents("/proc/meminfo")) {
                    $stats = explode("\n", str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats)); // Separate lines
                    foreach ($stats as $statLine) {
                        $statLineData = explode(":", trim($statLine));          // Separate values and find correct lines for total and free mem
                        if (count($statLineData) == 2 && trim($statLineData[0]) == "MemTotal") { // Total memory
                            $memoryTotal = trim($statLineData[1]);
                            $memoryTotal = explode(" ", $memoryTotal);
                            $memoryTotal = $memoryTotal[0];
                            $memoryTotal *= 1024;  // convert from kibibytes to bytes
                        }
                        if (count($statLineData) == 2 && trim($statLineData[0]) == "MemFree") { // Free memory
                            $memoryFree = trim($statLineData[1]);
                            $memoryFree = explode(" ", $memoryFree);
                            $memoryFree = $memoryFree[0];
                            $memoryFree *= 1024;  // convert from kibibytes to bytes
                        }
                    }
                } else {
                    print("No stats\n");
                }
            } else {
                print("/proc/meminfo not readable\n");
            }
        }
        return (is_null($memoryTotal) || is_null($memoryFree)) 
                ? ['0','0','0'] 
                : [ "used" => $this->formatFileSize($memoryTotal-$memoryFree), "total" => $this->formatFileSize($memoryTotal), "free" => $this->formatFileSize($memoryFree),"percent" => round(100 - ($memoryFree * 100 / $memoryTotal))];
    }

    /**
     * Makes more readable the display of the memory size 
     * 
     * @param int $bytes
     * @param bool $binaryPrefix
     * @return string
     */
    public function formatFileSize($bytes, $binaryPrefix=true) {
        if ($binaryPrefix) {
            $unit=['B','KiB','MiB','GiB','TiB','PiB'];
            if ($bytes==0) return '0 ' . $unit[0];
            return @round($bytes/pow(1024,($i=floor(log($bytes,1024)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
        } else {
            $unit=['B','KB','MB','GB','TB','PB'];
            if ($bytes==0) return '0 ' . $unit[0];
            return @round($bytes/pow(1000,($i=floor(log($bytes,1000)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
        }
    }

}