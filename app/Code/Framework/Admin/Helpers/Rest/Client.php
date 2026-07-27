<?php
namespace Code\Framework\Admin\Helpers\Rest;
use Humble;
use Log;
use Environment;
/**
 *
 * Rest Client functions
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Utility
 * @package    Other
 * @author     Rick rick@humbleprogramming.com
 */
class Client extends \Code\Framework\Admin\Helpers\Helper
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
    public function className() {
        return __CLASS__;
    }

}