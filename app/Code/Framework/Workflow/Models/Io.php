<?php
namespace Code\Framework\Workflow\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Input/Output workflow methods
 *
 * see title
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Io extends Model
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
    public function getClassName() {
        return __CLASS__;
    }

    /**
     * Will attempt to either link or attach one or more files to an event field
     * 
     * @param type $EVENT
     * @param type $data
     * @param type $config
     */
    protected function input($EVENT,$config) {
        $field = ($config['io_field'] ?? false);
        $dir   = ($config['io_directory'] ?? false);
        $type  = ($config['file_attach_type'] ?? 'link');
        $file  = ($config['io_file'] ?? false);
        $upd   = [ $field => null ];
        if ($field && $dir && is_dir($dir)) {
            if ($file == '*') {
                $dh             = dir($dir);
                $upd[$field]    = [];
                while ($entry   = $dh->read()) {
                    if (($entry == '.') || ($entry == '..')) {
                        continue;
                    }
                    $upd[$field][] = ($type == 'attach') ? file_get_contents($dir.DIRECTORY_SEPARATOR.$entry) : $dir.DIRECTORY_SEPARATOR.$entry;
                }
            } else {
                $upd[$field] = file_exists($dir.DIRECTORY_SEPARATOR.$file) ? (($type=='attach') ? file_get_contents($dir.DIRECTORY_SEPARATOR.$file) : $dir.DIRECTORY_SEPARATOR.$file) : '';
            }
        }
        $EVENT->update($upd);
    }
    
    /**
     * Will write either the entire event data to a file or just a single field to that file
     * 
     * @param type $EVENT
     * @param type $data
     * @param type $config
     */
    protected function output($EVENT,$data,$config) {
        $field = ($config['io_field'] ?? false);
        $dir   = ($config['io_directory'] ?? false);
        $file  = ($config['io_file'] ?? false);
        if ($field && $dir && is_dir($dir)) {
            file_put_contents($dir.DIRECTORY_SEPARATOR.$file, ($field=='*') ? json_encode($data,JSON_PRETTY_PRINT) : ($data[$field] ?? '') );
        }        
    }
    
    /**
     * Splits the IO process 
     * 
     * @param type $EVENT
     */
    public function process($EVENT=false) {
        if ($EVENT) {
            $data   = $EVENT->load();
            $config = $EVENT->fetch();
            switch ($config['io_type'] ?? false) {
                case "input" :
                    $this->input($EVENT,$config);
                    break;
                case "output":
                    $this->output($EVENT,$data,$config);
                    break;
                default      :
                    break;
            }
        }
        $d = $EVENT->load();
    }

}