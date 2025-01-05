<?php
namespace Code\Framework\Humble\Drivers;
use Humble;
use Log;
use Environment;
/**
 *
 * SQLLite Wrapper
 *
 * Methods to support SQLLite Integration
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class SQLLite extends ORM implements ORMEngine
{

    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    public function connect() {
        
    }
    
    public function query($query='') {
        
    }
    
    public function close() {
        
    }
}
