<?php
namespace Code\Base\Humble\Helpers;
use Environment;
/**
 *
 * Manages log functions
 *
 * Log functions
 *
 * PHP version 7.2+
 *
 * @category   Utility
 * @package    Framework
 * @author     Rick Myers rick@humbleprogramming.com
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Humble_Helpers_Log.html
 * @since      File available since Version 1.0.1
 */
class Log extends Helper
{
    private $logs   = array(
                    'error'     => '../../logs/&&NAMESPACE&&/error.log',
                    'warning'   => '../../logs/&&NAMESPACE&&/warning.log',
                    'general'   => '../../logs/&&NAMESPACE&&/general.log',
                    'mysql'     => '../../logs/&&NAMESPACE&&/mysql.log',
                    'mongodb'   => '../../logs/&&NAMESPACE&&/mongo.log',
                    'query'     => '../../logs/&&NAMESPACE&&/query.log',
                    'user'      => '../../logs/&&NAMESPACE&&/users/&&USERID&&.log'
                  );
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    public function processLogs() {
        $project = Environment::getProject();
        $user_id = $this->getUserId();
        foreach ($this->logs as $log => $location) {
            $this->logs[$log] = str_replace(['&&NAMESPACE&&','&&USERID&&'],[$project->namespace,$user_id],$location);
        }
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
     * Retrieves a certain amount of data from a specified log file using magic methods
     *
     * @return string
     */
    public function fetchLogData() {
        $this->processLogs();
        $data   = '';
        $log    = $this->getLog();
        $size   = $this->getSize();

        if (isset($this->logs[$log])) {
            if (!file_exists($this->logs[$log])) {
                $data =  'Log '.'['.$log.']'.$this->logs[$log]. ' does not exist';
            } else {
                $filesize = filesize($this->logs[$log]);
                $size     = (($size==='*') ? $filesize : ((((int)$size > (int) $filesize) ? $size : $filesize)));
                $size     = $size > 250000 ? 250000 : $size;                    //cap it at 250000
                $fh       = fopen($this->logs[$log],'r');
                $data     = fread($fh,$size);
            }
        } else {
            if ($log==='access') {
                $data = 'Viewing the access log is unavailable at this time';
            } else if ($log==='system') {
                $this->logs[$log] = ini_get('error_log');
                if (!trim($this->logs[$log])) {
                    $this->logs[$log] = '/var/log/httpd/error_log';
                }
                $filesize   = filesize($this->logs[$log]);
                $size       = (($size==='*') ? $filesize : (((int)$size > (int)$filesize) ? $filesize : $size));
                $size       = ($size > 200000) ? 200000 : $size;                //lets just make it easy on ourselves and limit it to 200K max
                $startfrom  = $filesize - $size;
                $fh         = fopen($this->logs[$log],'r');
                fseek($fh,$startfrom);
                $data = str_replace("\r\n","\n",fread($fh,$size+1));
            }
        }
        return $data;
    }

    /**
     * Writes over the log that was passed in
     */
    public function clearLog() {
        $this->processLogs();        
        $log = $this->getLog();
        if (isset($this->logs[$log])) {
            file_put_contents($this->logs[$log],'');   //blows away the log
        } else if ($log=='system') {
            $this->logs[$log] = ini_get('error_log');
            if (!trim($this->logs[$log])) {
                $this->logs[$log] = '/var/log/httpd/error_log'; // <-- Make this configurable!
            }
            file_put_contents($this->logs[$log],'');
        }
    }

    /**
     * Returns the list of active user logs
     * 
     * @return iterator
     */
    public function availableUserLogs() {
        $logs = [];
        $project = \Environment::getProject();
        $users = '../../logs/'.$project->namespace.'/users';

        $dh = dir($users);
        while ($entry = $dh->read()) {
            if (($entry=='.') || ($entry=='..') || ($entry=='anonymous.log')) {
                continue;
            }
            $logs[] = str_replace('.log','',$entry);
        }
        return \Humble::entity('humble/users')->usersById($logs);
    }

}