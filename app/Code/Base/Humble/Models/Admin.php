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
     * Creates a zip file containing JSON representing all the tables attributed to a namespace
     * 
     * @param type $namespace
     * @return type
     */
    public function export($namespace=false) {
        if ($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)) {
            $archive = new \ZipArchive();
            @mkdir('export',0775); 
            $file = $namespace.'_'.date('Ymd_His').'.zip';
            $archive->open('export/'.$file,\ZipArchive::CREATE);
            foreach (Humble::entity('humble/entities')->setNamespace($namespace)->fetch(true) as $entity) {
                $data = [];
                foreach (Humble::entity($namespace.'/'.$entity['entity'])->_polyglot('Y')->fetch() as $row) {
                    $data[] = $row;
                }
                $archive->addFromString($entity['entity'].'.json',json_encode($data,JSON_PRETTY_PRINT));
            }
            $archive->close();
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="'.$file.'"');
            $data =  file_get_contents('export/'.$file);
            unlink('export/'.$file);
            return $data;
        }
    }
    
    /**
     * Accepts a ZIP file containing JSON files to apply locally
     * 
     * @param type $namespace
     */
    public function import($namespace=false) {
        $message = 'Upload did not execute';
        if ($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)) {
            if ($data_file = $this->getDataFile()) {
                @mkdir('import',0775);
                copy($data_file['path'],'import/'.$data_file['name']);
                $archive = new \ZipArchive();
                if ($archive->open('import/'.$data_file['name'])) {
                    for ($i=0; $i<count($archive); $i++) {
                        $file = $archive->getFromIndex($i);
                        if ($name = $archive->getNameIndex($i)) {
                            $name = explode('.',$name);
                            $obs  = Humble::entity($namespace.'/'.$name[0]);
                            foreach (json_decode($file) as $row) {
                                $obs->reset();
                                foreach ($row as $field => $value) {
                                    $method = 'set'.$this->underscoreToCamelCase($field,true);
                                    $obs->$method($value);
                                }
                                $obs->save();
                            }
                        }
                    }
                }
                $archive->close();
                unlink('import/'.$data_file['name']);
                $message = 'Import finished, lets hope that worked';
            }
        }
        return $message;
    }
    
    /**
     * 
     * @param type $namespace
     */
    public function install($namespace=false) {
        if ($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)) {
            if ($module = $this->getModule()) {
                @mkdir('install',0775);
                copy($module['path'],'import/'.$module['name']);
                $archive = new \ZipArchive();
                if ($archive->open('import/'.$module['name'])) {
                    for ($i=0; $i<count($archive); $i++) {
                        $file = $archive->getFromIndex($i);
                    }
                }
            }
        }        
    }
    
    public function maintenance($enable=false) {
        $enable =  ($enable) ? $enable : ($this->getEnable() ? $this->getEnable() : false);
        chdir('..');
        $message = 'No flag was passed, so no action was taken';
        if ($enable==='Y') {
            if (file_exists('maint.html')) {
                rename('index.html','index2.html');
                rename('maint.html','index.html');
                $message = 'Now in maintenance mode';
            } else {
                $message = 'No maintenance stub page found, aborting';
            }
            
        } else {
            if (file_exists('index2.html')) {
                rename('index.html','maint.html');
                rename('index2.html','index.html');
                $message = 'Exited maintenance mode';
            } else {
                $message = 'Normal login page not found, aborting';
            }
        }
        chdir('app');
        return $message;
    }
}