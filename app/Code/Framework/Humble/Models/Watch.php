<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
            _______ __         
           / ____(_) /__       
          / /_  / / / _ \      
         / __/ / / /  __/      
  _     /_/_  /_/_/\___/   __  
 | |     / /___ _/ /______/ /_ 
 | | /| / / __ `/ __/ ___/ __ \
 | |/ |/ / /_/ / /_/ /__/ / / /
 |__/|__/\__,_/\__/\___/_/ /_/ 
                              
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Watch extends Model
{

    use \Code\Framework\Humble\Traits\EventHandler;

    private $handler    = null;
    private $directory  = null;
    private $extensions = [];
    
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
    public function className() {
        return __CLASS__;
    }

    /**
     * Record the handler that will trigger when a file change is detected
     * 
     * @param type $handler
     * @return $this
     */
    public function handler($handler=false) {
        if ($handler) {
            $this->handler($handler);
            return $this;
        }
        return $this->handler;
    }
    
    /**
     * Records what directory to watch, and what file extensions should be monitored
     * 
     * @param string $dirname
     * @param array $extensions
     * @return $this
     */
    public function directory($dirname=false,$extensions=[]) {
    
        return $this;
    }
}