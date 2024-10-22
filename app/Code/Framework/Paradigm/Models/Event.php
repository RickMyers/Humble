<?php
namespace Code\Framework\Paradigm\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Paradigm Engine Event Methods
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Workflow Editor
 * @author     Myers <>
 */
class Event extends Model
{

    use \Code\Framework\Humble\Traits\EventHandler;
	
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
     * Will get called when a new file is detected by the paradigm engine
     * 
     * @listen event(newFile)
     * @param type $EVENT
     */
    public function newFileTrigger($EVENT=false,$action='New') {
        $data = $EVENT->load();
        file_put_contents('event_data.txt',print_r($data,true));
    }
    
    /**
     * Will get called when an existing file is changed and is detected by the paradigm engine
     * 
     * @listen event(changedFile)
     * @param type $EVENT
     */
    public function changedFileTrigger($EVENT=false) {
        if ($EVENT) {
            $this->newFileTrigger($EVENT,'Changed'); 
        }
    }

    /**
     * Will get called when an existing file is deleted and is detected by the paradigm engine
     * 
     * @listen event(deletedFile)
     * @param type $EVENT
     */
    public function deletedFileTrigger($EVENT=false) {
        if ($EVENT) {
            $this->newFileTrigger($EVENT,'Deleted'); 
        }
    }   
}