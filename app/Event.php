<?php
/**
 * Static Factory methods for handling events
 *
 * PHP version 7.2+
 *
 * LICENSE:
 *
 * @category   Framework
 * @package    Core
 * @author     Original Author <rick@humbleprogramming.com>
 * @copyright  2007-Present, Rick Myers <rick@humbleprogramming.com>
 * @license    https://humbleprogramming.com/LICENSE.txt
 * @version    1.0.1
 * @since      File available since Version 1.0.1
 */
class Event {

    protected static $id = null;
    /**
     * Constructor
     */
    public function __construct() {
        //nop
    }

    /**
     * Be careful about the exclusionary behavior in this one... it removes stuff from the saved mongo object.  If you happen to
     * name a variable the same as one in the list below, you could lose data
     *
     * @param type $EVENT
     * @param type $mongoID
     * @return type
     */
    public static function set($EVENT,$mongoId) {
        $exclude = ['shape'=>true,'type'=>'true','configured'=>true,'_id'=>true,'id'=>true]; //this is unnecessary stuff to save
        $data    = Humble::collection('paradigm/elements')->setId($mongoId)->load();
        $cnf     = [];
        foreach ($data as $var => $val) {
            if (isset($exclude[$var])) {
                continue;
            }
            $cnf[$var] = $val;
        }
        $EVENT->target($mongoId);
        $EVENT->configurations($cnf);
        $EVENT->stages($mongoId);
        return $EVENT;
    }

    /**
     * I struggled with this one.  In the end, I abandoned the idea of "custom" event objects, and instead went with a common event object
     * using a reduced object model (no remote execution).  The reason I did so is that I'm not losing much, since magic methods can handle
     * most of the dynamic stuff I need to implement, and the common object keeps it simple.  Simplicity won out over the potential to
     * introduce complexity in the form of custom event methods
     *
     * @param type $identifier
     * @return \Humble\Event\Event
     */
    public static function get($identifier,$data=[]) {
        $event = new \Code\Framework\Humble\Event\Event($identifier);
        $name  = 'set'. underscoreToCamelCase($identifier,true);
        $event->$name($data);
        //DO I NEED THIS BELOW?
/*        foreach ($data as $key => $val) {
            $method = 'set'.ucfirst($key);
            $event->$method($val);
        }*/
        return $event;
    }

    /**
     * Returns a reference to a generic event trigger
     *
     * @return \Code\Framework\Humble\Event\Trigger
     */
    public static function getTrigger($eventTypeId=false) {
        return new \Code\Framework\Humble\Event\Trigger($eventTypeId);
    }

    /**
     * Specifically fires an event, useful from command line scripts
     *
     * @param object $EVENT
     * @param string $eventName
     * @return boolean
     */
    public static function trigger($eventName=false,$data=[]) {
        if ($eventName) {
            $trigger = self::getTrigger();
            return $trigger->emit($eventName,$data);
        }
        return false;
    }

    /**
     * Checks to see if an event has already been created by that event name
     *
     * @param string $eventName
     * @return type boolean
     */
    public static function isRegistered($eventName=false,$namespace=null) {
        $event_registered = false;
        if ($eventName) {
            $event            = Humble::entity('paradigm/events')->setEvent($eventName);
            if ($namespace) {
                $event->setNamespace($namespace);
            }
            $event_registered = (count($event->load(true)) > 0);
        }
        return $event_registered;
    }

    /**
     * Dynamically register an event, must specify at a minimum the event name and comment
     *
     * @param string $namespace
     * @param string $eventName
     * @param string $comment
     * @param string $eventType
     * @return int
     */
    public static function register($namespace=null,$eventName=false,$comment=false,$eventType='CUSTOM') {
        $id = false;
        if ($eventName && $comment) {
            if ($eventType     = Humble::entity('paradigm/event/types')->setEvent($eventType)->load(true)) {
                $event = Humble::entity('paradigm/events')->setEvent($eventName)->setEventTypeId($eventType['id'])->setComment($comment);
                if ($namespace) {
                    $event->setNamespace($namespace);
                }
                $id = $event->save();
            };
        }
        return $id;
    }

    /**
     *
     */
    public function __clone()        {        }
    public function __wakeup()       {        }
}
?>