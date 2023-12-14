<?php
namespace Code\Framework\Humble\Entities\Service;
use Humble;
use Log;
use Environment;
/**
 *
 * Service Directory Queries
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Entity
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Directory extends \Code\Framework\Humble\Entities\Entity
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Overriding default fetch behavior to jazz it up a lil
     * 
     * @param boolean $useKey
     * @return iterator
     */
    public function fetch($useKey=false) {
        $hide_clause = ($this->getHideFrameworkServices()=='true') ? "and namespace not in ('humble','paradigm','workflow')" : "";
        $ns_clause   = ($this->getNamespace()) ? "and namespace = '".$this->getNamespace()."' " : "";
        $query = <<<SQL
          select * from humble_service_directory
           where id is not null
           {$hide_clause}
           {$ns_clause}
SQL;
        return $this->query($query);
    }
}
