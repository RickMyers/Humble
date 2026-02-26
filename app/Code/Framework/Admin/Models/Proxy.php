<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Command Proxy methods
 *
 * The command proxy performs actions as 'root'
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Proxy extends Model
{

    use \Code\Framework\Humble\Traits\EventHandler;
	
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
     * Persists the return code
     * 
     * @param int $val
     * @return int
     */
    public function _RC($val=false) {
        if ($val) {
            $this->RC = $val;
        }
        return $this->RC;
    }
    
    /**
     * Will check to see if the Command Proxy is currently running
     * 
     * @return bool
     */
    public function check() {
        if ($running = file_exists('PIDS/proxy.pid')) {
            if ($this->windows) {
                return $running;
            }
            exec('ps -aux | grep Proxy.php',$results);
            $running = false;
            foreach ($results as $row) {
                $row     = preg_replace('/\s+/', ' ', $row);
                $section = explode(" ",$row);
                $running = $running || (($section[10]=='php') && ($section[11]=='Proxy.php'));
            }
        } 
        $rc = $running ? $this->_RC(0) : $this->_RC(4);                         //if it is running return 0, otherwise want that it isnt with a 4
        return $running;
    }    
}