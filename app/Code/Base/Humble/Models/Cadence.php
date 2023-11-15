<?php
namespace Code\Base\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Cadence methods
 *
 * Methods used to control our cadence poller
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Cadence extends Model
{

    use \Code\Base\Humble\Traits\EventHandler;
	
    private $RC = null;
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
     * Puts a command, or stacks one, to the cadence command control file
     * 
     * @param type $command
     * @return $this
     */
    protected function addCommand($command=false) {
        if ($command) {
            $contents   = json_decode(file_exists('cadence.cmd') ? file_get_contents('cadence.cmd') : "[]");
            $contents[] = $command;
            return file_put_contents('cadence.cmd',json_encode($contents,JSON_PRETTY_PRINT));
        }
        return $this;
    }
    /**
     * Will check to see if Cadence is currently running
     * 
     * @return bool
     */
    public function check() {
        if ($running = file_exists('cadence.pid')) {
            exec('ps -aux | grep Cadence.php',$results);
            $running = false;
            foreach ($results as $row) {
                $row = preg_replace('/\s+/', ' ', $row);
                $section = explode(" ",$row);
                $running = $running || (($section[10]=='php') && ($section[11]=='Cadence.php'));
            }
        } 
        $rc = $running ? $this->_RC(0) : $this->_RC(4);                         //if it is running return 0, otherwise want that it isnt with a 4
        return $running;
    }
    
    /**
     * Starts the poller
     */
    public function start() {
        $this->_RC(8);
        $message = 'Cadence Is Already Running...';
        if (!$this->check()) {
            exec('nohup php Cadence.php > /dev/null &');
            $message = "Cadence Started...";
            $this->_RC(0);
           }
        return $message;
     }
     
    /**
     * Starts the poller
     */
    public function restart() {
        $this->_RC(8);
        $message = 'Cadence Not Running';
        if ($this->check()) {
            $this->addCommand("RESTART");
            $message = "Cadence Restarting... Will Stop Shortly";
            $this->_RC(0);        
        }
        return $message;
     }
        
    /**
     * Stops the poller
     */
    public function stop() {
        $this->_RC(8);
        $message = "Cadence Not Running";
        if ($this->check()) {
            $this->addCommand('END');
            $message = "Cadence Quiescing... Will Stop Shortly";
            $this->_RC(0);
        }
        return $message;
    }
    
    /**
     * Restarts the poller
     */
    public function reload() {
        $this->_RC(8);
        $message = "Cadence Not Running";
        if ($this->check()) {
            $this->addCommand("RELOAD");
            $message = "Cadence Reloading... Will Be Done Shortly";
            $this->_RC(0);
        }
    }
    

}