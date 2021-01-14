<?php
namespace Code\Base\Humble\Event;
use Humble;
/**
 *
 * Maintains state for workflow events
 *
 * This is the event that gets passed around workflows
 *
 *
 * PHP version 7.2+
 *
 * @category   Logical Model
 * @package    Event
 * @author     Rick Myers <rick@humblecoding.com>
 * @copyright  2007-Present, Rick Myers <rick@humblecoding.com>
 * @license    https://humblecoding.com/license.txt
 * @version    1.0
 * @link       https://humblecoding.com/docs/class-Core_Model_Event.html
 * @since      File available since Version 1.0.1
 */
class BaseObject  {

    private     $_configurations = [];     //Custom configurations for stages (if applicable)
    private     $_initiated      = null;   //Time the event was triggered
    private     $_completed      = false;  //The time the event was finished
    private     $_stages         = [];     //A list of the methods that were traversed
    private     $_ref            = null;   //reference to the mongo collection
    private     $_id             = null;   //mongo ID
    private     $_data           = [];     //magic methods data collector
    private     $_component      = null;   //holds a reference to the classname that spawned the event
    private     $_method         = null;   //holds a reference to the method that triggered the event
    private     $_status         = false;  //Was the result of the workflow a positive or negative outcome
    private     $_name           = null;   //Name of the event
    private     $_namespace      = null;   //Namespace the event was triggered under
    private     $_bubble         = true;   //To bubble or not to bubble, that's the question this answers
    private     $_target         = false;  //The current stage in the workflow being processed
    private     $_eventErrors    = [];     //A list of all errors encountered with respect to the event itself
    private     $_errors         = [];     //Errors encountered when stages interracted with the event or during a workflow
    private     $_alerts         = [];
    private     $_files          = [];
    private     $_reports        = [];

    //These two variables are YUGE! (Trump Joke)
    private     $instance        = '';      //Every time you clone this object, the instance counter will get incremented
    static private $instances    = 0;       //For the original object, the empty string leaves the MongoID intact

    /**
     * Not sure about this.  It might be more than necessary.
     *
     * @TODO: Research whether we need to actually save the event between elements in the workflow.  It might be too expensive.
     *
     * @param type $identifier
     */
    public function __construct($identifier='') {
        $this->_ref(Humble::getCollection('paradigm/events'));
        $this->_name($identifier);                          //what is my event name
        $this->_initiated(array('date'=>date('Y-m-d H:i:s'),'timestamp'=>time()));             //the event and the workflow are related, this records essentially when the workflow was kicked off
        $doc = $this->save();                               //initial save to get an ID
        $this->_id($doc['_id'].$this->instance);            //assign generated id to Event ID
    }

    /**
     * Ensures that the event information is persisted
     */
    public function __destruct() {
        if (!(php_sapi_name() === 'cli')) {
            if (count($this->_alerts)) {
                header('Alerts: '.json_encode($this->_alerts));
            }
        }
        $this->close();
        $this->save();  //one last save.... is it really needed?

    }

    /**
     * For debugging purposes, this needs to be in every class but entities
     *
     * @return classname
     */
    public function getClassName() {
        return __CLASS__;
    }


    /**
     * Returns the data attached to the original event
     *
     * @return array
     */
    public function load() {
        $method = 'get'.ucfirst($this->_name);
        return $this->$method();
    }

    /**
     * Returns the configuration for the current stage
     *
     * @return type
     */
    public function fetch() {
        return $this->_configurations[$this->_target()];
    }

    /**
     * Builds the event header and serializes the magic method data
     *
     * @return \Code\Base\Humble\Models\Mongo
     */
     public function save() {
        $ref = $this->_ref();
        $ref->setName($this->_name());
        $ref->setStatus($this->_workflowStatus());
        $ref->setBubble($this->_bubble());
        $ref->setEvent([
            'namespace' => $this->_namespace(),
            'component' => $this->_component(),
            'method'    => $this->_method(),
            'initiated' => $this->_initiated(),
            'completed' => $this->_completed()
        ]);
        $ref->setFiles($this->_files());
        $ref->setErrors($this->_errors);
        $ref->setEventErrors($this->_eventErrors);
        $ref->setStages($this->_stages);
        $ref->setReports($this->_reports);
        $ref->setConfigurations($this->_configurations);
        foreach ($this->_data as $var => $val) {
            $method = 'set'.ucfirst($var);
            $ref->$method($val);
        }
        if ($this->_id()) {
            $ref->set_id($this->_id());
            $document = $ref->save();
        } else {
            $document = $ref->add();
            if ($document['_id']) {
                $this->_id($document['_id']);
            }
        }
        return $document;
    }

    /**
     * This appends to the original event data some new information, if there's already a node of the same name and the node is not an array, then the node is converted to an array with an initial value of the original value
     *
     * @param type $newData
     */
    public function update($newData=[],$persist=false) {
        if (is_array($newData)) {
            $getter  = 'get'.ucfirst($this->_name());
            $setter  = 'set'.ucfirst($this->_name());
            $this->$setter(array_merge_recursive($this->$getter(),$newData));
        } else {
            $this->error('An attempt was made to update the core event attributes rather than creating a node off of the core event attributes');
        }
        if ($persist) {
            $this->save();
        }
        return $this;
    }
    
    /**
     * Just a synonym for the update method, if you like the 'attach' verb better than the 'update' verb
     * 
     * @param type $newData
     * @param type $persist
     * @return $this
     */
    public function attach($newData=[],$persist=false) {
        $this->update($newData,$persist);
        return $this;
    }

    /**
     * Replaces something in the initial event (like a password) with something else
     *
     * @param type $newData
     */
    public function replace($newData) {
        $getter     = 'get'.ucfirst($this->_name());
        $setter     = 'set'.ucfirst($this->_name());
        $eventData  = $this->$getter();
        foreach ($newData as $field => $val) {
            if (isset($eventData[$field])) {
                $eventData[$field] = $val;
            }
        }
        $this->$setter($eventData);
    }

    /**
     * Finalizes the last stage and finishes...
     */
    public function close() {
        $x = count($this->_stages);
        if ($x) {
            $this->_stages[$x-1]['finished'] = time();
        }
        $this->_completed(true);
    }

    /**
     * Allows you to retrieve a value from the original triggering event data
     *
     * @param type $name
     * @return type
     */
    public function data($name=false) {
        $retVal = null;
        if ($name) {
            $retVal = isset($this->_data[$this->_name()][$name]) ? $this->_data[$this->_name()][$name] : false;
        }
        return $retVal;
    }

    /**
     * The unique ID (_id) of the Mongo object we are using to persist the state of the event
     *
     * @param type $arg
     * @return type
     */
    public function _id($arg=false) {
        if ($arg !== false) {
            $this->_id = $arg;
            return $this;
        }
        return $this->_id;
    }

    /**
     * Retains a reference to the Mongo object
     *
     * @param type $obj
     * @return type
     */
    public function _ref($obj=false) {
        if ($obj !== false) {
            $this->_ref = $obj;
            return $this;
        }
        return $this->_ref;
    }

    /**
     * Gets the configurations values for the current element being processed in a workflow
     *
     * @param type $data
     * @return type
     */
    public function _configurations($data=false) {
        if ($data!==false) {
            $this->_configurations[$this->_target()] = $data;
            return $this;
        }
        return $this->_configurations;
    }

    /**
     * Either sets or gets what the current element being processed is in a workflow
     *
     * @param type $id
     * @return type
     */
    public function _target($id=false) {
        if ($id) {
            $this->_target = $id;
            return $this;
        }
        return $this->_target;
    }

    /**
     * Keeps track of the individual stages in the workflow that were processed
     *
     * @param type $id
     */
    public function _stages($id=false) {
        if ($id!==false) {
            if ($x = count($this->_stages)) {
                $this->_stages[$x-1]['finished'] = time();
            }
            $this->_stages[] = ['id'=>$id,'started'=>time(),'finished'=>null];
            return $this;
        }
        return $this->_stages;
    }


    /**
     * Accessor/Mutator for the name of the event
     *
     * @param type $arg
     * @return type
     */
    public function _name($arg=false) {
        if ($arg !== false) {
            $this->_name = $arg;
            return $this;
        }
        return $this->_name;
    }

    /**
     * Accessor/Mutator for which namespace the triggering method was in
     *
     * @param type $arg
     * @return type
     */
    public function _namespace($arg=false) {
        if ($arg !== false) {
            $this->_namespace = $arg;
            return $this;
        }
        return $this->_namespace;
    }

    /**
     * Accessor/Mutator for which component the triggering method was in
     *
     * @param type $class
     * @return string
     */
    public function _component($class=false) {
        if ($class!==false) {
            $this->_component = $class;
            return $this;
        } 
        return $this->_component;
    }

    /**
     * Accessor/Mutator for which method fired the event
     *
     * @param type $method
     * @return string
     */
    public function _method($method=false) {
        if ($method) {
            $this->_method = $method;
            return $this;
        }
        return $this->_method;
    }

    /**
     * Records when the workflow was started
     *
     * @param type $arg
     * @return type
     */
    private function _initiated($arg=false) {
        if ($arg !== false) {
            $this->_initiated = $arg;
            return $this;
        }
        return $this->_initiated;
    }

    /**
     * Accessor/Mutator for whether we allow the event to "bubble" to additional workflows
     *
     * @param type $bubble
     * @return type
     */
    public function _bubble($bubble=null) {
        if ($bubble===null) {
            return $this->_bubble;
        } else {
            $this->_bubble = $bubble;
            return $this;
        }
    }

    /**
     * Records when the workflow was finished
     *
     * @param type $now
     * @return unix timestamp
     */
    public function _completed($now=false) {
        if ($now) {
            $this->_completed = array('date'=>date('Y-m-d H:i:s'),'timestamp'=>time());
            return $this;
        }
        return $this->_completed;
     }

    /**
     * Saves whether the workflow had a positive or negative outcome...
     *
     * @param type $status
     * @return boolean
     */
    public function _workflowStatus($status=null) {
        if ($status === null) {
            return $this->_status;
        } else {
            $this->_status = $status;
            return $this;
        }
    }

    /**
     * Will either attach the contents of a file to the event, or just the filename
     *
     * @param type $filename
     * @param type $attach
     */
    public function _files($filename=false,$attach=false) {
        if ($filename) {
            if ($attach) {
                $this->_files[$filename] = file_exists($filename) ? file_get_contents($filename) : false;
            } else {
                $this->_files[] = $filename;
            }
            return $this;
        } else {
            return $this->_files;
        }
    }

    /**
     * Will either attach the contents of a file to the event, or just the filename
     *
     * @param type $filename
     * @param type $attach
     */
    public function _reports($filename=false,$attach=false) {
        if ($filename) {
            if ($attach) {
                $this->_reports[$filename] = file_exists($filename) ? file_get_contents($filename) : false;
            } else {
                $this->_reports[] = $filename;
            }
            return $this;
        } else {
            return $this->_reports;
        }
    }

    /**
     * Stages should call this method to record errors that occurred during execution... such as insufficient data in the event
     *
     * @param type $message
     * @return type
     */
    public function error($message=false) {
        if ($message) {
            $this->_errors[] = $message;
            return $this;
        }
        return count($this->_errors) ? $this->_errors[count($this->_errors)-1] : false;
    }

    /**
     * returns the last error recorded
     *
     * @return string
     */
    public function lastError() {
        return (($this->_errors) ? $this->_errors(count($this->_errors)-1) : null);
    }

    /**
     * Stages should call this method to record errors that occurred during execution... such as insufficient data in the event
     *
     * @param type $message
     * @return type
     */
    public function alert($message=false) {
        if ($message) {
            $this->_alerts[] = $message;
        }
        return count($this->_alerts) ? $this->_alerts[count($this->_alerts)-1] : false;
    }

    /**
     * Errors that occur as a result of internal event handling
     *
     * @param string $msg
     * @return string
     */
    public function _eventError($msg=null) {
        if ($msg!==null) {
            $this->_eventErrors[] = $msg;
            return $this;
        }
        return count($this->_eventErrors) ? $this->_eventErrors[count($this->_eventErrors)-1] : false;
    }

    /**
     * Returns a value from a magic method
     *
     *
     * @param string $name A mnemonic, label or variable name
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

    /**
     * When we "clone" (or copy) an event, this method is called, and it lets us update the mongo_id so we dont get a duplicate while also mainting a way to identify this events "parent"
     */
    public function __clone() {
        //$this->_id($this->_id().$this->instance);            //assign generated id to Event ID
        //ok, i really need to do this, but when?
        $this->instance = ++self::$instances;
    }
}