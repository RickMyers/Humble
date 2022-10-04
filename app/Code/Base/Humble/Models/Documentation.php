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

    use \Code\Base\Humble\Event\Handler;
    
    private string $documentor          = 'PHPDoc2.phar';
    private string $documentor_source   = 'https://phpdoc.org/phpDocumentor.phar';
	
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
     * Checks to see if the default documentor is already downloaded, and if not, then we go get it
     * 
     * @TODO: Allow at the system level to choose which documentation tool you wish to use
     * @return boolean
     */
    protected function documentorExists() {
        if (!$exists = file_exists($this->documentor)) {
            $exists = file_put_contents($this->documentor,file_get_contents($this->documentor_source));
        }
        return $exists;
    }
    
    /**
     * Runs the command to generate inline API documentation
     * 
     * @workflow use(PROCESS) emit(APIDocumentationGenerated)
     * @return string
     */
    public function generate($EVENT=false) {
        $results = 'Documentation Generation Error';
        if (Environment::getApplication('state') !== 'PRODUCTION') {
            chdir('..');
            if ($this->documentorExists()) {
                $results = shell_exec(PHP_BINARY.' PHPDoc2.phar 2>&1');
                if ($EVENT) {
                    $EVENT->update(['documentation_generation_results'=>$results]);
                }
                //$this->trigger('APIDocumentationGenerated',__CLASS__,__METHOD__,['generated'=>date('Y-m-d H:i:s'),'generator'=>Environment::whoAmIReally()]);
            } else {
                $results = 'Error doing stuff';
            }
            chdir('app');
        }
        return $results;
    }
}