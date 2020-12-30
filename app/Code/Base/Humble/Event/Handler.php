<?php
namespace Code\Base\Humble\Event;
use Humble;
use Event;
use Environment;
use Log;
/**
 *
 * Any class that wants to throw an event needs to "use" this trait...
 *
 */
trait Handler {

    protected $_errors  = [];    //you can set a singular error message. Otherwise use the _errors() for multiple
    protected $_EVENTS  = [];   //reference to the events being managed

    /**
     * Retrieves workflows and initiates the event propagation
     *
     * @param type $EVENT
     */
    public function notify($cleanEvent=false,$name) {
        global $workflowRC;
        global $cancelBubble;
        $ok             = true;
        $uid            = Environment::whoAmI();
        if (!$uid) {
            //if no user id, see if this is the login event, and if so, find user based on username
            if ($user_name = $cleanEvent->data('user_name')) {
                $user   = Humble::getEntity('humble/users')->setUserName($user_name)->load(true);
                $uid    = isset($user['uid']) ? $user['uid'] : 0;  //why not 0?
            }
        }
        Humble::getEntity('paradigm/event/log')->setEvent($name)->setUserId($uid)->setMongoId($cleanEvent->_id())->save();
        if ($cleanEvent) {
            $workflows  = Humble::getEntity('paradigm/workflow/listeners');              //now lookup if any workflows are "listening", and then include them
            $namespace = $cleanEvent->_namespace();
            $component = $cleanEvent->_component();
            $method    = $cleanEvent->_method();
            if (!$component === 'Event') {
                $workflows->setNamespace($namespace);
                $workflows->setComponent($component);
                $workflows->setMethod($method);
                foreach ($workflows->active() as $diagram) {
                    if ($diagram['active']==='Y') {
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
                        $EVENT->_bubble(false); //why?
                        $EVENT->_workflowStatus($workflowRC);
                        $EVENT->close();
                        if ($cancelBubble) {
                            $this->_errors('bubbling was canceled');
                            break;  //time to exit! No more workflow processing
                        }
                        $this->_event($EVENT);
                    }
                }
            } else {
                Event::getTrigger()->fire($namespace,$cleanEvent,$name);
            }
        }
        return $ok;
    }

    /**
     * Stores a copy of an event
     *
     * @param type $arg
     * @return type
     */
    private function _event($arg=false) {
        if ($arg!==false) {
            $this->_EVENTS[] = $arg;
        }
        return $this->_EVENTS;
    }

    /**
     * Primes the event header and loads the initial event before passing to the notify propagation routine
     *
     * @param type $EVENT
     */
    protected function trigger($name,$model,$method,$data=[]) {
        $model  = explode('\\',$model);
        $EVENT  = Event::get($name);
        $EVENT->_namespace($this->_namespace());
        $EVENT->_component($model[count($model)-1]);                     //This is why we can't have models in subdirectories...
        $EVENT->_method(explode('::',$method)[1]);
        $action = 'set'.ucfirst($name);
        $EVENT->$action($data);
        return $this->notify($EVENT,$name);
    }
    
    /**
     * Preps and relays an event to the notify method
     * 
     * @param string $name
     * @param string $namespace
     * @param array $data
     * @return EVENT
     */
    protected function fire($namespace='',$name,$data=[]) {
        $EVENT  = Event::get($name);
        $EVENT->_namespace($namespace);
        $EVENT->_component('Event');                     //This is why we can't have models in subdirectories...
        $EVENT->_method($name);
        $action = 'set'.ucfirst($name);
        $EVENT->$action($data);
        return $this->notify($EVENT,$name);
    }
    
    /**
     * Relay for the fire method
     * 
     * @param type $name
     * @param type $data
     * @return type
     */
    protected function emit($name,$data) {
        return $this->fire($this->_namespace(),$name,$data);
    }
    
    /**
     * Allows you to call a workflow component directly by simulating an event
     *
     * @param type $data
     * @param type $method
     */
    public function spawn($name=false,$namespace=false,$controller=false,$method=false,$data=[]) {
        if ($name && $data && $method) {
            $EVENT  = Event::get($name);
            $EVENT->_namespace($namespace);
            $EVENT->_component($controller);
            $EVENT->_method($method);
            $action = 'set'.ucfirst($name);
            $EVENT->$action($data);
            return $this->notify($EVENT);
        }
        return false;
    }


    /**
     * Returns the last error, if any
     *
     * @param type $msg
     * @return type
     */
    public function _error($msg=null) {
        if ($msg!==null) {
            $this->_errors[] = $msg;
        } else {
            return $this->_errors[count($this->errors)-1];  //return the last error encountered
        }
    }
}