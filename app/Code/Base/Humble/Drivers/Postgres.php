<?php
namespace Code\Base\Humble\Drivers;
use Humble;
use Log;
use Environment;
/**
 *
 * Postgres Wrapper
 *
 * Methods to support PostGRE SQL Integration
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Postgres extends ORM implements ORMEngine
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