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
     * Starts the poller
     */
    public function start() {
        
    }
    
    /**
     * Stops the poller
     */
    public function stop() {
        
    }
    
    /**
     * Restarts the poller
     */
    public function reload() {
        
    }
    

}