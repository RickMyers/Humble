<?php
namespace Code\Framework\Humble\Event;
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
 * @author     Rick Myers <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/license.txt
 * @version    1.0
 * @link       https://humbleprogramming.com/docs/class-Model_Event.html
 * @since      File available since Version 1.0.1
 */
class Trigger  {
    use \Code\Framework\Humble\Traits\EventHandler;

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


    /**
     * Tries to run a workflow by including it
     * 
     * @global boolean $cancelBubble
     * @global type $workflowRC
     * @param type $diagram
     * @param type $cleanEvent
     */
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
                $this->_error('The source file for workflow '.$source.' was not found');
            }
            $EVENT->_workflowStatus($workflowRC);
            $EVENT->close();
            $this->_event($EVENT);
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param type $namespace
     * @param type $cleanEvent
     * @param type $eventName
     */
    public function fire($namespace,$cleanEvent,$eventName) {
        foreach (Humble::entity('paradigm/event/listeners')->setNamespace($namespace)->setEvent($eventName)->setActive('Y')->fetch() as $diagram) {
            if (count(Humble::entity('paradigm/workflows')->setWorkflowId($diagram['workflow_id'])->setActive('Y')->load(true))) {
                $handled = $this->runWorkflow($diagram,clone $cleanEvent);
                if ($cancelBubble) {
                    $this->_error('bubbling was canceled');
                    break;  //time to exit! No more workflow processing
                }
            }
        }
    }
    /**
     * We are looking for listeners for our event.  A listener could be a workflow listener, a method listener, or a system listener.  Once we find one, we mark the event as handled
     * 
     * If you have a workflow event, and a method listener or system listener of the same name, only 1 of them will get executed.  
     *
     * @param string $eventName
     * @param array $arguments
     */
    public function emit($eventName=false,$arguments=[]) {
        global $workflowRC;
        global $cancelBubble;
        $ok         = true;
        $uid        = \Environment::whoAmI();
        $cleanEvent = Event::get($eventName);
        $results    = [];
        $action     = 'set'.underscoreToCamelCase($eventName,true);
        $cleanEvent->$action(array_merge($this->_arguments,$arguments));
        $output     = '';
        if (!$uid) {
            //if no user id, see if this is the login event, and if so, find user based on username
            if ($user_name = $cleanEvent->data('user_name')) {
                $user   = Humble::entity('default/users')->setUserName($user_name)->load(true);
                $uid    = isset($user['uid']) ? $user['uid'] : 0;  
            }
        }
//        Humble::entity('paradigm/event/log')->setEvent($eventName)->setUserId($uid)->setMongoId($cleanEvent->_id())->save();
        if ($cleanEvent) {
            foreach (Humble::entity('paradigm/workflow/listeners')->setNamespace($this->_namespace())->setMethod($eventName)->setActive('Y')->fetch() as $diagram) {
                $results[$diagram['id']] = $this->runWorkflow($diagram,$cleanEvent);
                if ($cancelBubble) {
                    $this->_error('bubbling was canceled');
                    break;  //time to exit! No more workflow processing
                }
            }
            foreach (Humble::entity('paradigm/event/listeners')->setNamespace($this->_namespace())->setEvent($eventName)->setActive('Y')->fetch() as $diagram) {
                if (count(Humble::entity('paradigm/workflows')->setWorkflowId($diagram['workflow_id'])->setActive('Y')->load(true))) {
                    $results[$diagram['id']] = $this->runWorkflow($diagram,$cleanEvent);
                    if ($cancelBubble) {
                        $this->_error('bubbling was canceled');
                        break;  //time to exit! No more workflow processing
                    }
                }
            }
            $method_listeners = Humble::entity('paradigm/method/listeners');
            if ($namespace = ($this->_namespace()) ? $this->_namespace() : false) {
                $method_listeners->setNamespace($namespace);
            }
            foreach ($method_listeners->setEvent($eventName)->fetch() as $method_listener) {
                $ml     = Humble::model($method_listener['namespace'].'/'.$method_listener['class']);
                $method = $method_listener['method'];
                ob_start();
                $ml->$method($cleanEvent);
                $output .= ob_get_clean();
            }
        }
        //what am I going to do with the results array?
        return $output;
    }

    /**
     * The idea here is that I want to spawn off the workflow in its own thread.  I'll get around to this eventually... but it kills the idea of bubbling/cancel-bubble
     * 
     * @param type $EVENT
     */
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
        $token = lcfirst(substr($name,3));
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