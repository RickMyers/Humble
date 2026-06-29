<?php
namespace Code\Framework\Humble\Event;
use Humble;

class MyEvent extends \ArrayIterator {
    
    private     array  $configurations  = [];     //Custom configurations for stages (if applicable)
    private     string $initiated       = '';     //Time the event was triggered
    private     string $completed       = '';     //The time the event was finished
    private     array  $stages          = [];     //A list of the methods that were traversed
    private     mixed  $ref             = null;   //reference to the mongo collection
    private     string $id              = '';     //mongo ID
    private     array  $data            = [];     //magic methods data collector
    private     int    $data_idx        = 0;    
    private     string $component       = '';     //holds a reference to the classname that spawned the event
    private     string $method          = '';     //holds a reference to the method that triggered the event
    private     bool   $status          = false;  //Was the result of the workflow a positive or negative outcome
    private     string $name            = '';     //Name of the event
    private     string $namespace       = '';     //Namespace the event was triggered under
    private     bool   $bubble          = true;   //To bubble or not to bubble, that's the question this answers
    private     string $target          = '';     //The current stage in the workflow being processed
    private     array  $eventErrors     = [];     //A list of all errors encountered with respect to the event itself
    private     array  $errors          = [];     //Errors encountered when stages interracted with the event or during a workflow
    private     array  $alerts          = [];
    private     array  $files           = [];
    private     array  $reports         = [];

    private     mixed  $instance        = '';      //Every time you clone this object, the instance counter will get incremented
    static private $instances           = 0;       //For the original object, the empty string leaves the MongoID intact

    /**
     * Not sure about this.  It might be more than necessary.
     *
     * @TODO: Research whether we need to actually save the event between elements in the workflow.  It might be too expensive.
     *
     * @param type $identifier
     */
    public function __construct($identifier='') {
        $this->ref(Humble::collection('paradigm/events'));
        $this->name($identifier);                                               //what is my event name
        $this->initiated(['date'=>date('Y-m-d H:i:s'),'timestamp'=>time()]);    //the event and the workflow are related, this records essentially when the workflow was kicked off
        $doc = $this->save();                                                   //initial save to get an ID
        $this->id($doc['_id'].$this->instance);                                 //assign generated id to Event ID
    }

    /**
     * Converts underscore characters to the next uppercase character in the string
     * 
     * @param type $string
     * @param type $first_char_caps
     * @return type
     */
    protected function underscoreToCamelCase($string, $first_char_caps=false) : string {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    } 
    
    /**
     * Polymorphic accessor/mutator for the mongo ID
     * 
     * @param type $mongoID
     * @return $this
     */
    public function id($mongoId=false) : mixed {
        if ($mongoId) {
            $this->id = $mongoId;
            return $this;
        }
        return $this->id;
    }
    
    /**
     * Just a relay
     * 
     * @return string
     */
    public function getId() : string {
        return $this->id();
    }
    
    /**
     * Ensures that the event information is persisted
     */
    public function __destruct() {
        if (!(php_sapi_name() === 'cli')) {
            if (count($this->alerts)) {
                header('Alerts: '.json_encode($this->alerts));
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
    public function className() : string {
        return __CLASS__;
    }

    /**
     * Returns the number of elements in our array
     * 
     * @return int
     */
    public function count() : int {
        return count($this->data);
    }

    /**
     * If  you are able to iterate to the next element of the array, then move the data_idx up by 1
     * 
     * @return void
     */
    public function next() : void {
        if ($this->valid()) {
            $this->data_idx = $this->data_idx + 1;
        }
    }
    
    /**
     * Moves the current element array index to a specified offset if that offset exists
     * 
     * @param int $offset
     * @return void
     */
    public function seek(int $offset) : void {
        if ($this->offsetExists($offset)) {
            $this->data_idx = $offset;
        }
    }
    
    /**
     * Returns whether a particular offset exists in our array
     * 
     * @param mixed $key
     * @return bool
     */
    public function offsetExists(mixed $key) : bool {
        return (isset($this->data[$key]));
    }
    
    /**
     * Returns the current key location, which is held in the data_idx variable
     * 
     * @return int|null
     */
    public function key() : int|null {
        return $this->data_idx;
    }

    /**
     * Returns the value at the current index location, or null if there is no value at that location
     * 
     * @return mixed
     */
    public function current() : mixed {
        return isset($this->data[$this->data_idx]) ? $this->data[$this->data_idx] : null;
    }
    
    /**
     * Just sets the array index back to 0
     * 
     * @return void
     */
    public function rewind() : void {
        $this->data_idx = 0;
    }
    
    /**
     * Adds an element to the end of the array
     * 
     * @param mixed $value
     * @return void
     */
    public function append(mixed $value) : void {
        $this->data[$this->count()] = $value;
    }
    
    /**
     * Returns whether there are more elements in the array to iterate over
     * 
     * @return bool
     */
    public function valid() : bool {
        return $this->data_idx < ($this->count() - 1);
    }
    
    /**
     * Returns the data attached to the original event
     *
     * @return array
     */
    public function load() : array {
        $method = 'get'.$this->underscoreToCamelCase($this->name,true);
        return $this->$method();
    }

    /**
     * Rebuilds the data portion of the event
     * 
     * @param array $data
     * @return $this
     */
    protected function set($data=false) : mixed {
        if ($data) {
            $method = 'set'.$this->underscoreToCamelCase($this->name,true);
            $this->$method($data);            
        }
        return $this;
    }
    
    /**
     * Returns the configuration for the current stage
     *
     * @return type
     */
    public function fetch() {
        return $this->configurations[$this->target()];
    }

    /**
     * Converts the data and configuration parts of the event to a JSON string
     * 
     * @return type
     */
    public function serialize() : string {
        return json_encode([
            "data"   => $this->load(),
            "config" => $this->fetch() 
        ]);
    }
    
    /**
     * Rebuilds the data portion and configuration portion of an event
     * 
     * @param type $data
     * @return $this
     */
    public function deserialize($event=false) {
        if ($event) {
            $this->set($data['data']);
            $this->configurations[$this->target()] = $data['config'];
        }
        return $this;
    }
    
    /**
     * Builds the event header and serializes the magic method data
     *
     * @return \Code\Framework\Humble\Models\Mongo
     */
     public function save() {
        $ref = $this->ref();
        $ref->setName($this->name());
        $ref->setStatus($this->workflowStatus());
        $ref->setBubble($this->bubble());
        $ref->setEvent([
            'namespace' => $this->namespace(),
            'component' => $this->component(),
            'method'    => $this->method(),
            'initiated' => $this->initiated(),
            'completed' => $this->completed()
        ]);
        $ref->setFiles($this->files());
        $ref->setErrors($this->errors);
        $ref->setEventErrors($this->eventErrors);
        $ref->setStages($this->stages);                                         //STAGES AND CONFIGURATIONS SHOULD ME BE COMBINED
        $ref->setReports($this->reports);
        $ref->setConfigurations($this->configurations);
        foreach ($this->data as $var => $val) {
            $method = 'set'.$this->underscoreToCamelCase($var,true);
            $ref->$method($val);
        }
        if ($this->id()) {
            $ref->set_id($this->id());
            $document = $ref->save();
        } else {
            $document = $ref->add();
            if ($document['_id']) {
                $this->id($document['_id']);
            }
        }
        return $document;
    }

    /**
     * This appends to the original event data some new information, if there's already a node of the same name and the node is not an array, then the node is converted to an array with an initial value of the original value
     * 
     * @param type $newData
     * @param type $allowOverride
     * @param type $persist
     * @return $this
     */
    public function update($newData=[],$allowOverride=false,$persist=false) {
        $updated = false;
        if (is_array($newData)) {
            $getter  = 'get'.$this->underscoreToCamelCase($this->name(),true);
            $setter  = 'set'.$this->underscoreToCamelCase($this->name(),true);
            $data       = $this->$getter();
            foreach ($newData as $field => $values) {
                if (!isset($data[$field]) || (isset($data[$field]) && $allowOverride)) {
                    $data[$field] = $values;
                }
            }
            $this->$setter($data);
            //$this->$setter(array_merge_recursive($this->$getter(),$newData));
            $updated = true;
        } else if ($allowOverride) {
            $updated = true;
        } else {
            $this->error('An attempt was made to update the core event attributes rather than creating a node off of the core event attributes');
        }
        if ($updated && $persist) {
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
        $getter     = 'get'.$this->underscoreToCamelCase($this->name(),true);
        $setter     = 'set'.$this->underscoreToCamelCase($this->name(),true);
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
        $flow = [];
        foreach ($this->stages as $idx => $stage) {
            if (isset($stage['component'])) {
                $stage['component'] = $this->configurations[$idx];
                if (isset($this->configurations[$stage['id']])) {
                    foreach ($this->configurations[$stage['id']] as $name => $value) {
                        $stage[$name] = $value;
                    }
                }
            }
            $flow[$idx] = $stage;
        }
        $this->setFlow($flow);        
        $x = count($this->stages);
        if ($x) {
            $this->stages[$x-1]['finished'] = time();
        }
        $this->completed(true);
        return $this;
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
            $retVal = isset($this->data[$this->name()][$name]) ? $this->data[$this->name()][$name] : false;
        }
        return $retVal;
    }

    /**
     * Retains a reference to the Mongo object
     *
     * @param type $obj
     * @return type
     */
    public function ref($obj=false) {
        if ($obj !== false) {
            $this->ref = $obj;
            return $this;
        }
        return $this->ref;
    }

    /**
     * Gets the configurations values for the current element being processed in a workflow
     *
     * @param type $data
     * @return type
     */
    public function configurations($data=false) {
        if ($data!==false) {
            $this->configurations[$this->target()] = $data;
            return $this;
        }
        return $this->configurations;
    }

    /**
     * Either sets or gets what the current element being processed is in a workflow
     *
     * @param type $id
     * @return type
     */
    public function target($id=false) {
        if ($id) {
            $this->target = $id;
            return $this;
        }
        return $this->target;
    }

    /**
     * Keeps track of the individual stages in the workflow that were processed
     *
     * @param type $id
     */
    public function stages($id=false) {
        if ($id!==false) {
            if ($x = count($this->stages)) {
                $this->stages[$x-1]['finished'] = time();
            }
            $this->stages[] = ['id'=>$id,'started'=>time(),'finished'=>null];
            return $this;
        }
        return $this->stages;
    }


    /**
     * Accessor/Mutator for the name of the event
     *
     * @param type $arg
     * @return type
     */
    public function name($arg=false) {
        if ($arg !== false) {
            $this->name = $arg;
            return $this;
        }
        return $this->name;
    }

    /**
     * Accessor/Mutator for which namespace the triggering method was in
     *
     * @param type $arg
     * @return type
     */
    public function namespace($arg=false) {
        if ($arg !== false) {
            $this->namespace = $arg;
            return $this;
        }
        return $this->namespace;
    }

    /**
     * Accessor/Mutator for which component the triggering method was in
     *
     * @param type $class
     * @return string
     */
    public function component($class=false) {
        if ($class!==false) {
            $this->component = $class;
            return $this;
        } 
        return $this->component;
    }

    /**
     * Accessor/Mutator for which method fired the event
     *
     * @param type $method
     * @return string
     */
    public function method($method=false) {
        if ($method) {
            $this->method = $method;
            return $this;
        }
        return $this->method;
    }

    /**
     * Records when the workflow was started
     *
     * @param type $arg
     * @return type
     */
    private function initiated($arg=false) {
        if ($arg !== false) {
            $this->initiated = $arg;
            return $this;
        }
        return $this->initiated;
    }

    /**
     * Accessor/Mutator for whether we allow the event to "bubble" to additional workflows
     *
     * @param type $bubble
     * @return type
     */
    public function bubble($bubble=null) {
        if ($bubble===null) {
            return $this->bubble;
        } else {
            $this->bubble = $bubble;
            return $this;
        }
    }

    /**
     * Records when the workflow was finished
     *
     * @param type $now
     * @return unix timestamp
     */
    public function completed($now=false) {
        if ($now) {
            $this->completed = array('date'=>date('Y-m-d H:i:s'),'timestamp'=>time());
            return $this;
        }
        return $this->completed;
     }

    /**
     * Saves whether the workflow had a positive or negative outcome...
     *
     * @param type $status
     * @return boolean
     */
    public function workflowStatus($status=null) {
        if ($status === null) {
            return $this->status;
        } else {
            $this->status = $status;
            return $this;
        }
    }

    /**
     * Will either attach the contents of a file to the event, or just the filename
     *
     * @param type $filename
     * @param type $attach
     */
    public function files($filename=false,$attach=false) {
        if ($filename) {
            if ($attach) {
                $this->files[$filename] = file_exists($filename) ? file_get_contents($filename) : false;
            } else {
                $this->files[] = $filename;
            }
            return $this;
        } else {
            return $this->files;
        }
    }

    /**
     * Will either attach the contents of a file to the event, or just the filename
     *
     * @param type $filename
     * @param type $attach
     */
    public function reports($filename=false,$attach=false) {
        if ($filename) {
            if ($attach) {
                $this->reports[$filename] = file_exists($filename) ? file_get_contents($filename) : false;
            } else {
                $this->reports[] = $filename;
            }
            return $this;
        } else {
            return $this->reports;
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
            $this->errors[] = $message;
            return $this;
        }
        return count($this->errors) ? $this->errors[count($this->errors)-1] : false;
    }

    /**
     * returns the last error recorded
     *
     * @return string
     */
    public function lastError() {
        return (($this->errors) ? $this->errors[count($this->errors)-1] : null);
    }

    /**
     * Stages should call this method to record errors that occurred during execution... such as insufficient data in the event
     *
     * @param type $message
     * @return type
     */
    public function alert($message=false) {
        if ($message) {
            $this->alerts[] = $message;
        }
        return count($this->alerts) ? $this->alerts[count($this->alerts)-1] : false;
    }

    /**
     * Errors that occur as a result of internal event handling
     *
     * @param string $msg
     * @return string
     */
    public function eventError($msg=null) {
        if ($msg!==null) {
            $this->eventErrors[] = $msg;
            return $this;
        }
        return count($this->eventErrors) ? $this->eventErrors[count($this->eventErrors)-1] : false;
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
            if (isset($this->data[$name])) {
                $retval = $this->data[$name];
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
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * Magic method to handle non-existent methods being invoked
     *
     * Whenever a method is called that doesn't exist, this method traps the name
     * of the method, and any arguments.
     *
     * @param string $name The name of the method
     * @param array $arguments arguments passed to the non-existant method
     */
    public function __call($name, $arguments)    {
        $token = lcfirst(substr($name,3));
        if (substr($name,0,3)==='set') {
            return $this->_set($token,$arguments[0]);
        } else if (substr($name,0,3)==='get') {
            $result = $this->_get($token);
            return $result;
        } else {
            \Log::console("Undefined Method: ".$name." invoked from ".$this->className().".");
        }
    }

    /**
     * When we "clone" (or copy) an event, this method is called, and it lets us update the mongo_id so we dont get a duplicate while also maintaining a way to identify this events "parent"
     */
    public function __clone() {
        $this->instance = ++self::$instances;
    }
    
}