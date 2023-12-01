<?php
namespace Code\Base\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Admin User Actions
 *
 * Methods specifically for performing administrator actions
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <>
 */
class User extends Model
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