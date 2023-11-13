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
     * Puts a command, or stacks one, to the cadence command control file
     * 
     * @param type $command
     * @return $this
     */
    protected function addCommand($command=false) {
        if ($command) {
            $contents = json_decode(file_exists('cadence.cmd') ? file_get_contents('cadence.cmd') : '[]');
            return file_put_contents('cadence.cmd',json_encode($contents[] = $command,JSON_PRETTY_PRINT));
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
            exec('ps -aux',$results);
            print_r($results);
        } 
        
        return $running;
    }
    
    /**
     * Starts the poller
     */
    public function start() {
        $message = "Failed To Start";
        if (!$this->check()) {
            exec('php Cadence.php &');
            $message = "Cadence Started...";
        }
        return $message;
     }
    
    /**
     * Stops the poller
     */
    public function stop() {
        $message = "Cadence Not Running";
        if ($this->check()) {
            $this->addCommand("END");
            $message = "Cadence Quiescing... Will Stop Shortly";
        }
        return $message;
        
        
    }
    
    /**
     * Restarts the poller
     */
    public function reload() {
        $message = "Cadence Not Running";
        if ($this->check()) {
            $this->addCommand("RELOAD");
            $message = "Cadence Reloading... Will Be Done Shortly";
        }
    }
    

}