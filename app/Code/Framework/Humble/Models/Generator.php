<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * A workflow "generator" testbed
 *
 * see Title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Other
 * @author     Rick <rick@humbleprogramming.com>
 */
class Generator extends Model
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
    public function className() {
        return __CLASS__;
    }

    /**
     * Just a test to see if the cloning and yielding is working
     * 
     * @workflow use(generator) config(workfow/field/generator)
     * @param type $EVENT
     */
    public function testGen($EVENT=false) {
        if ($EVENT) {
            for ($i=0; $i<=10; $i++) {
                yield $EVENT->clone([]);
            }
            yield null;
        }
    }
}