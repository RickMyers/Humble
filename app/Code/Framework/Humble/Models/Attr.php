<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Attribute Test
 *
 * see desc
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    humble
 * @author     Rick <rick@humbleprogramming.com>
 */
class Attr extends Model
{

    
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

}