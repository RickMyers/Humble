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
 * @author     Richard Myers <rick@enicity.com>
 * @copyright  2007-Present, Rick Myers <rick@enicity.com>
 * @license    https://enicity.com/license.txt
 * @version    <INSERT VERSIONING MECHANISM HERE />
 * @link       https://enicity.com/docs/class-&&MODULE&&.html
 * @since      File available since Version 1.0.1
 */
class Detector extends Model
{

    use \Code\Base\Humble\Event\Handler;

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
        if ($EVENT) {
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
                foreach (Humble::getEntity('paradigm/sensor_workflows')->setSensor($cnfg['field'])->fetchActive() as $workflow) {
                    $EVENT = clone $cleanEvent; //each spawned workflow gets its own event
                    if (file_exists('Workflows/'.$workflow['workflow_id'].'.php')) {
                        include('Workflows/'.$workflow['workflow_id'].'.php');
                    }
                }
            }
        }
    }
}