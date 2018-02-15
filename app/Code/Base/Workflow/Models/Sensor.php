<?php
namespace Code\Base\Workflow\Models;
use Humble;
use Log;
use Environment;
/**    
 *
 * Sensor Methods
 *
 * Things relating to sensors in workflows
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
class Sensor extends Model
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
     * Now we correlate the sensor to the workflow
     *
     */
    private function registerSensorWorkflow($id,$data) {
        $sensor = Humble::getEntity('paradigm/sensor_workflows');
        $sensor->setSensorId($id);
        $sensor->setWorkflowId($data['workflow_id']);
        if ($data['sensor']==='other') {
            $sensor->setValue($data['other_value']);
        } else {
            $sensor->setValue($data['sensor']);
        }
        $sensor->save();
    }

    /**
     * We use the normal component save here, and then register the sensor
     *
     */
    public function save() {
        $data       = json_decode($this->getData(),true);
        $this->setWindowId($data['windowId']);  //now I need a shower...
        $component  = Humble::getModel('workflow/manager');
        $component->setData($this->getData());
        $component->saveComponent();
        $sensor     = Humble::getEntity('paradigm/sensors');
        if ($data['sensor']==='other') {
            $sensor->setSensor($data['other_value']);
        } else {
            $sensor->setSensor($data['sensor']);
        }
        $sensor->setSensorId($data['id']);
        $id = $sensor->save();
        $this->registerSensorWorkflow($id,$data);
    }

}