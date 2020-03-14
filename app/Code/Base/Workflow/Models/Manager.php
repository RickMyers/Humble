<?php
namespace Code\Base\Workflow\Models;
use Humble;
/**
 * General purpose Model file for configuring workflow elements
 *
 * The methods in this class are used to populate the configuration windows
 *  inside the paradigm workflow editor.  The specific methods here are not
 *  visible though, so you can't select them. They are used by the workflow
 *  editor and not directly by the user or the components themselves
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Internal Utilities (Not visible from the workflow editor)
 * @package    Base
 * @taxonomy   class(utilities,workflow)
 * @author     Original Author <rick@humblecoding.com>
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://license.enicity.com
 * @version    0.0.1
 * @since      File available since Version 1.0.1
 */
class Manager extends Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * All Classes are requried to have this method
     *
     * @return system
     */
    public function getClassName() {
        return __CLASS__;
    }

    /**
     *
     */
    public function saveComponent() {
        $data    = json_decode($this->getData(),true);
        $element = Humble::getCollection('paradigm/elements');
        $element->setId($data['id']);
        $b = $element->load();
        $this->setType($b['type']);
        foreach ($data as $var => $val) {
            $method = 'set'.ucfirst($var);
            $this->$method($val);
            if ($var == 'windowId') {
                //we don't need to record this, since it changes each time you edit this component
                continue;
            }
            $element->$method($val);
        }
        $element->setConfigured(1);
        return $element->save();
    }

    /**
     * When you save off an actor trigger, you must set a listener for the event as well as save the data regarding the event
     *
     */
    public function setListener() {
        $id       = $this->saveComponent();
        $data     = json_decode($this->getData(),true);
        $this->setWindowId($data['windowId']);
        $listener = Humble::getEntity('paradigm/workflow_listeners');
        $listener->setNamespace($data['namespace']);
        $listener->setComponent($data['component']);
        $listener->setMethod($data['method']);
        $listener->setWorkflowId($data['workflow_id']);
        $listener->save();
        return $id;
    }
}

?>