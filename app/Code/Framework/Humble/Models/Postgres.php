<?php
namespace Code\Framework\Humble\Models;
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

    /**
     * Returns information, per DB engine, about the entities within
     * 
     * @return array
     */
    public function listEntities() {
        $entities = [];
        return $entities;
    }
    
    public function addLimit($page=1) {
        
    }
    
    public function buildOrderByClause() {
        
    }
    
    public function buildWhereClause($useKeys=false) {
        
    }
    
    public function calculateStats($noLimitQuery='',&$results=[]) {
        
    }
    
    public function close() {
        
    }
    
}