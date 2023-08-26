<?php
namespace Code\Base\Paradigm\Models;
use Humble;
use Event;
use Log;
/**
 * 
 * Workflow Detector
 *
 * Methods used by the workflow detector, searching for name value pairs
 * in the event
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Workflow
 * @author     Richard Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Detector extends Model
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
     * Will look for workflows to trigger based on the presence of name value pairs in the EVENT
     *
     * @param type $EVENT
     */
    public function trigger($EVENT=false) {
        if ($EVENT!==false) {
            $data   = $EVENT->load();
            $cnfg   = $EVENT->fetch();
            if (isset($data[$cnfg['field']])) {
                //we are going to get a brand new event for this...
                $method = 'setDetected'.ucfirst($cnfg['field']);
                $cleanEvent = Event::get('detected'.ucfirst($cnfg['field']));
                $cleanEvent->$method($data);
                $cleanEvent->_namespace('paradigm');
                $cleanEvent->_component('detector');
                $cleanEvent->_method('trigger');
                foreach (Humble::entity('paradigm/sensor_workflows')->setSensor($cnfg['field'])->fetchActive() as $workflow) {
                    $EVENT = clone $cleanEvent; //each spawned workflow gets its own event
                    if (file_exists('Workflows/'.$workflow['workflow_id'].'.php')) {
                        include('Workflows/'.$workflow['workflow_id'].'.php');
                    }
                }
            }
        }
    }
}