<?php
namespace Code\Framework\Admin\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Tailwind related methods
 *
 * see description
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Tailwind extends Model
{

    private $modules = [];
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        foreach (Humble::entity('humble/modules')->fetch() as $module) {
            $this->modules[$module['namespace']] = $module;
        }
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
     * Attempt to start an instance of tailwind per a specific module by way of namespace
     * 
     * @return int
     */
    public function start($namespace=false) {
        $rc         = 16;
        $namespace  = ($namespace ? $namespace : $this->getNamespace());
        if ($module = $this->modules[$namespace]) {
            $args = [
                'program'   => 'tailwind',
                'root'      => $root = 'Code/'.$module['package'].'/'.$module['module'].'/web/tailwind',
                'command'   => 'npm run watch',
                'namespace' => $namespace
            ];
            $cmd = 'nohup php RunWrapper.php "'.addslashes(json_encode($args)).'" > /dev/null 2>&1 &';
            exec($cmd,$results,$rc);
        }   
        return $rc;
    }
    
    /**
     * Attempt to stop an instance of tailwind running for a particular namespace
     * 
     * @return int
     */
    public function stop($namespace=false):int {
        $rc         = 16;
        $namespace  = ($namespace ? $namespace : $this->getNamespace());
        if ($module = $this->modules[$namespace]) {
            if (file_exists($pid_file = 'PIDS/tailwind_'.$namespace.'.pid')) {
                if (posix_kill(file_get_contents($pid_file),15)) {
                   $rc = unlink($pid_file);
                }
            }
        }
        return $rc;
    }

    /**
     * Check to see if tailwind is installed for this module
     * 
     * @return boolean
     */
    public function check($namespace=false):bool {
        $installed = false;
        if ($module = $this->modules[$namespace = ($namespace ? $namespace : $this->getNamespace())]) {
            $installed = (file_exists('Code/'.$module['package'].'/'.$module['module'].'/web/tailwind/package.json'));
        }
        
        return $installed;
    }
    
    /**
     * Check to see if tailwind is running
     * 
     * @return boolean
     */
    public function running($namespace=false):bool {
        $running = false;
        if ($namespace = ($namespace ? $namespace : $this->getNamespace())) {
            if (file_exists($pid_file  = 'PIDS/tailwind_'.$namespace.'.pid')) {
                //do a check
            }
        }
        return $running;
    }
    
    /**
     * Install the tailwind package...
     * 
     * @return boolean
     */
    public function install($namespace=false):int {
        $rc         = 16;        
        $namespace  = ($namespace ? $namespace : $this->getNamespace());
        if ($module = $this->modules[$namespace]) {
            $cmd    = 'php CLI.php --tailwind ns='.$namespace;
            exec($cmd,$results,$rc);
        }
        return $rc;
    }
}