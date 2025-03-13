<?php
namespace Code\Framework\Workflow\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Input/Output workflow methods
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Io extends Model
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

    protected function input($EVENT,$data) {
        
    }
    
    protected function output($EVENT,$data) {
        
    }
    
    public function process($EVENT=false) {
        if ($EVENT) {
            $data   = $EVENT->load();
            $config = $EVENT->fetch();
            switch ($data['io_type'] ?? false) {
                case "input" :
                    $this->input($EVENT,$data);
                    break;
                case "output":
                    $this->output($EVENT,$data);
                    break;
                default      :
                    break;
            }
        }
    }

}