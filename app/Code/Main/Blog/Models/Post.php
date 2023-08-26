<?php
namespace Code\Main\Blog\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Blog Methods
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Post extends Model
{

    use \Code\Base\Humble\Traits\EventHandler;

	
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
     * Will this work?
     * 
     * @listen event(methodEventTest)
     * @param type $EVENT
     */
    public function anotherTest($EVENT=false) {
        if ($EVENT) {
            $x = $EVENT->load();
            print('world');
        }
    }    
    
    /**
     * Will this work?
     * 
     * @listen event(methodEventTest)
     * @param type $EVENT
     */
    public function thisTest($EVENT=false) {
        if ($EVENT) {
            $x = $EVENT->load();
            print('hello');
        }
    }    
    
    /**
     * When any of the events below get called, this method will fire
     * 
     * @listen event(listenerTest,testAction)
     * @param type $EVENT
     */
    public function listenerAction($EVENT=false) {
        Log::general('Listener Was Called');
        Log::general($EVENT);
        print_r($EVENT->load());
    }
    
    public function doTest() {
        print('running test'."\n\n");
        $this->emit('listenerTest',['whoami'=>'a donut']);
    }
}