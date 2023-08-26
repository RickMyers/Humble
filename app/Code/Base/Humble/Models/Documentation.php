<?php
namespace Code\Base\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Documentation related methods
 *
 * By default, we are going to use PHPDoc-2, but we may support APIGEN <3
 * in the future
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Core
 * @author     Rick Myers <rick@humbleprogramming.com>
 */
class Documentation extends Model
{

    use \Code\Base\Humble\Traits\EventHandler;
    
    //--------------------------------------------------------------------------
    //We will just default to PHP Documentor just for S&Gs
    private $documentor          = 'PHPDoc2.phar';                           //The default documentation engine
    private $documentor_source   = 'https://phpdoc.org/phpDocumentor.phar';  //Where the copy of documentor is stored
    private $command             = 'PHPDoc2.phar';                       //Default execution string
    private $location            = '/usr/bin/php';                           //Default location of the PHP engine
	
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->location = Environment::PHPLocation();
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
     * Checks to see if the default documentor (or other) is already downloaded, and if not, then we go get it
     * 
     * @TODO: Allow at the system level to choose which documentation tool you wish to use
     * @return boolean
     */
    protected function documentorExists() {
        if ($documentor = Environment::getApplication('documentation',true)) {
            $this->documentor        = isset($documentor['engine'])   ? $documentor['engine']   : false;
            $this->documentor_source = isset($documentor['source'])   ? $documentor['source']   : false;
            $this->command           = isset($documentor['command'])  ? $documentor['command']  : false;
        }
        if (!$exists = file_exists('../'.$this->documentor)) {
            $exists = file_put_contents('../'.$this->documentor,file_get_contents($this->documentor_source));
            shell_exec('chmod +x '.'../'.$this->documentor);
        }
        return $exists;
    }
    
    /**
     * Runs the command to generate inline API documentation, though generating documentation in production is forbidden
     * 
     * @workflow use(PROCESS) emit(APIDocumentationGenerated)
     * @return string
     */
    public function generate($EVENT=false) {
        $results = 'Documentation Generation Error';
        if (Environment::getApplication('state') !== 'PRODUCTION') {
            if ($this->documentorExists()) {
                chdir('..');
                $cmd     = Environment::PHPLocation().' '.$this->command.' 2>&1';
                $results = shell_exec($cmd);
                if ($EVENT) {
                    $EVENT->update(['documentation_generation_results'=>$results]);
                }
                chdir('app');
                //$this->trigger('APIDocumentationGenerated',__CLASS__,__METHOD__,['generated'=>date('Y-m-d H:i:s'),'generator'=>Environment::whoAmIReally()]);
            } else {
                $results = 'Could not resolve the documentation engine to use';
            }
        }
        return $results;
    }
}