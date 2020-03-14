<?php
namespace Code\Base\Humble\Event;
use Humble;
use Event;
use Log;
/**
 *
 * Triggers events from within a controller
 *
 * This is a wrapper for when we trigger events from the controllers, as specified in the controllers XML
 *
 *
 * PHP version 7.2+
 *
 * @category   Workflow
 * @package    Event
 * @author     Rick Myers <rick@humblecoding.com>
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://enicity.com/license.txt
 * @version    1.0
 * @link       https://enicity.com/docs/class-Core_Model_Event.html
 * @since      File available since Version 1.0.1
 */
class Trigger  {
    use Handler;

    private $_arguments  = [];
    private $_data       = [];
    private $_name       = null;
    private $_namespace  = null;
    private $_controller = null;
    private $_method     = null;


    /**
     * For debugging purposes, this needs to be in every class but entities
     *
     * @return classname
     */
    public function getClassName() {
        return __CLASS__;
    }

    /**
    */
    public function _controller($arg=false) {
        if ($arg !== false) {
            $this->_controller = $arg;
        } else {
            return $this->_controller;
        }
    }

    /**
     * Wrapper for triggering events from controllers, as opposed to models
     *
     * @return type
     */
    public function execute($name) {
        $EVENT  = Event::get($name);
        $EVENT->_namespace($this->_namespace());
        $EVENT->_component($this->_controller());
        $EVENT->_method($this->_method());
        $action = 'set'.ucfirst($name);
        $EVENT->$action($this->_arguments);
        return $this->notify($EVENT,$name);
    }


    protected function runWorkflow($diagram = false,$cleanEvent) {
        global $cancelBubble;
        global $workflowRC;
        $ok = false;
        if ($diagram && isset($diagram['active']) && ($diagram['active'] == 'Y')) {
            $EVENT = clone $cleanEvent; //this event is dirty
            $source = 'Workflows/'.$diagram['workflow_id'].'.php';
            if (file_exists($source)) {
                $workflowRC     = null;
                $cancelBubble   = false;
                include($source);
                $ok = $ok && $workflowRC;
            } else {
                $this->_errors('The source file for workflow '.$source.' was not found');
            }
            $EVENT->_workflowStatus($workflowRC);
            $EVENT->close();
            $this->_event($EVENT);
        }
    }

    /**
     * Used when an event is identified on a controller... specifically calls those workflows just looking for a particular event name and not based on namespace/controller/method
     *
     * @param type $eventName
     */
    public function emit($eventName=false) {
        global $workflowRC;
        global $cancelBubble;
        $ok         = true;
        $uid        = \Environment::whoAmI();
        $cleanEvent = Event::get($eventName);
        $action     = 'set'.ucfirst($eventName);
        $cleanEvent->$action($this->_arguments);
        if (!$uid) {
            //if no user id, see if this is the login event, and if so, find user based on username
            if ($user_name = $cleanEvent->data('user_name')) {
                $user   = Humble::getEntity('humble/users')->setUserName($user_name)->load(true);
                $uid    = isset($user['uid']) ? $user['uid'] : 0;  
            }
        }
        Humble::getEntity('paradigm/event/log')->setEvent($eventName)->setUserId($uid)->setMongoId($cleanEvent->_id())->save();
        if ($cleanEvent) {
            $handled = false;
            foreach (Humble::getEntity('paradigm/workflow/listeners')->setNamespace($this->_namespace())->setMethod($eventName)->fetch() as $diagram) {
                $handled = $this->runWorkflow($diagram,$cleanEvent);
                if ($cancelBubble) {
                    $this->_errors('bubbling was canceled');
                    break;  //time to exit! No more workflow processing
                }
            }
            if (!$handled) {
                $f = Humble::getEntity('paradigm/event/listeners');
                $n = $this->_namespace();
                foreach (Humble::getEntity('paradigm/event/listeners')->setNamespace($this->_namespace())->setEvent($eventName)->setActive('Y')->fetch() as $diagram) {
                    $handled = $this->runWorkflow($diagram,$cleanEvent);
                    if ($cancelBubble) {
                        $this->_errors('bubbling was canceled');
                        break;  //time to exit! No more workflow processing
                    }
                }
                //we didn't find a listener for our event, lets try it another way
            }
        }
        return $ok;
    }

    public function spawn($EVENT) {

    }


    /**
     *
     * @return type
     */
    public function _name($arg=false) {
        if ($arg !== false) {
            $this->_name = $arg;
        } else {
            return $this->_name;
        }
    }

    /**
     *
     * @return type
     */
    public function _namespace($arg=false) {
        if ($arg !== false) {
            $this->_namespace = $arg;
        } else {
            return $this->_namespace;
        }
    }

    /**
     *
     * @return type
     */
    public function _method($arg=false) {
        if ($arg !== false) {
            $this->_method = $arg;
        } else {
            return $this->_method;
        }
    }

    public function _arguments($field=false,$value=null) {
        if ($field && ($value!==null)) {
            $this->_arguments[$field] = $value;
        } else if ($field) {
            return $this->_arguments[$field];
        } else {
            return $this->_arguments;
        }
    }

    /**
     * Returns a value from a magic method
     *
     * @param string $name A pnuemonic, label or variable name
     *
     * @return string Variable value or response from remote resource
     */
    public function __get($name)  {
        $retval = null;
        if (!is_array($name)) {
            if (isset($this->_data[$name])) {
                $retval = $this->_data[$name];
            }
        }
        return $retval;
    }

    /**
     * The setter magic method.
     *
     * Just stores the name/value pair passed in to the internal array.
     *
     * @param string $name Name of variable in the name/value pair
     *
     * @param string $value Value of variable in the name/value pair
     */
    public function __set($name,$value) {
        $this->_data[$name] = $value;
        return $this;
    }

    /**
     * Magic method to handle non-existant methods being invoked
     *
     * Whenever a method is called that doesn't exist, this method traps the name
     * of the method, and any arguments.
     *
     * @param string $name The name of the method
     * @param array $arguments arguments passed to the non-existant method
     */
    public function __call($name, $arguments)    {
        $token = substr($name,3);
        $token{0} = strtolower($token{0});
        if (substr($name,0,3)=='set') {
            return $this->__set($token,$arguments[0]);
        } else if (substr($name,0,3)=='get') {
            $result = $this->__get($token);
            return $result;
        } else {
            \Log::console("Undefined Method: ".$name." invoked from ".$this->getClassName().".");
        }
    }

}