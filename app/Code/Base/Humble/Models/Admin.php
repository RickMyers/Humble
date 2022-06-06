<?php
namespace Code\Base\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Administration related actions
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Admin extends Model
{

    use \Code\Base\Humble\Event\Handler;

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
     * 
     * @param type $namespace
     */
    public function export($namespace=false) {
        if ($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)) {
            $archive = new \ZipArchive();
            @mkdir('export',0775); 
            $file = $namespace.'_'.date('Ymd_His').'.zip';
            $archive->open('export/'.$file,\ZipArchive::CREATE);
            foreach (Humble::getEntity('humble/entities')->setNamespace($namespace)->fetch() as $entity) {
                $data = [];
                foreach (Humble::getEntity($namespace.'/'.$entity['entity'])->_polyglot('Y')->fetch() as $row) {
                    $data[] = $row;
                }
                $archive->addFromString($entity['entity'].'.json',json_encode($data));
            }
            $archive->close();
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="'.$file.'"');
            $data =  file_get_contents('export/'.$file);
            unlink('export/'.$file);
            return $data;
        }
    }
    
    public function import($namespace=false) {
        if ($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)) {
        }        
    }
    
    public function install($namespace=false) {
        if ($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)) {
        }        
    }
}