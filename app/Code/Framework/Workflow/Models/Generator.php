<?php
namespace Code\Framework\Workflow\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Generator implementation
 *
 * Various generators for general use
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Paradigm Engine
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
     * Splits an input file into multiple output files and propagates events pointing to new files
     * 
     * @workflow use(generator) configuration(workflow/generator/splitter)
     * @param type $EVENT
     */
    public function fileSplitter($EVENT=false) {
        $data = $EVENT->load();
        $cnfg = $EVENT->fetch();
        if ($EVENT) {
            for ($i=0; $i<=10; $i++) {
                yield $EVENT->clone([]);
            }
            yield null;
        }
    }
}