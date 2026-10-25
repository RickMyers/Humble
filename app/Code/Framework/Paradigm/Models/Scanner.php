<?php
namespace Code\Framework\Paradigm\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Element Scanner
 *
 * Scans for workflow related components
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Scanner extends Model
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
    public function className() {
        return __CLASS__;
    }

    public function scan($what=false) {
        print("I am scanning $what\n");
    }
    
    /**
     * Find just the comment portion of the doc comment
     *
     * @param ReflectionClass $md
     * @param Object $method
     * @return string
     */
    protected function processDocComments($md=false,$method=false) {
        $skip = array('/**'=>true, '/'=>true, '*/'=>true, ''=>true, '/**/'=>true);
        $comments = [];
        try {
            $docComments = explode("\n",$md->getDocComment());
            foreach ($docComments as $comment) {
                $comment = trim($comment);                                                      //left align
                $comment = (substr($comment,0,1)=='*') ? trim(substr($comment,1)) : $comment;   //remove the * if there
                if (substr($comment,0,1)=='@') {
                    continue;                                                                   //we got an annotation, not looking for that...
                }
                if (!isset($skip[$comment])) {
                    $comments[] = $comment;
                }
            }
        } catch (\ReflectionException $ex) {
         //  \Log::console($ex);
        }
        return implode("\n",$comments);
    }
        
    /**
     * Fetches the document comment (if any) and looks for any of our custom annotations, saving those into an array, and returning that array to the calling routine
     *
     * @param ReflectionClass $md
     * @param Object $method
     * @return array
     */
    protected function processDocAnnotations($md=false,$method=false) {
        $components = [];
        try {
            $comments = explode("\n",$md->getDocComment());
            foreach ($comments as $comment) {
            if ((strpos($comment,'@workflow')!==false) || (strpos($comment,'@listen')!==false))  {
                    $components[] = substr($comment,strpos($comment,'@')+1);
                }
            }
        } catch (\ReflectionException $ex) {
         //  \Log::console($ex);
        }
        return $components;
    }
        
    /**
     * Will search through a modules PHP components and record any that are
     * listed as being workflow components
     *
     */
    public function registerWorkflowComponents($namespace=false) {
        $skip = ['MySQL'     => true,
                 'ORM'       => true,
                 'Postgres'  => true,
                 'SQLLite'   => true,
                 'SQLServer' => true];
        $namespace          = ($namespace) ? $namespace : (($this->getNamespace()) ? $this->getNamespace() : null); //add $this->_namespace()?
        $models             = Humble::getModels($namespace);
        $workflowComponent  = Humble::entity('paradigm/workflow/components');
        $workflowComment    = Humble::entity('paradigm/workflow/comments');
        $this->output("WORKFLOW","Processing Namespace [".$namespace."]...");
        foreach ($models as $model) { 
            if ($namespace === 'humble') {
                if (isset($skip[$model])) {
                    continue;
                }
            }
            $workflowComponent->setNamespace($namespace);
            $workflowComponent->setComponent($model);
            $class          = Humble::model($namespace.'/'.$model);
            if (!method_exists($class, 'className')) {
                continue;
            }
            $name           = $class->className();
            $reflection     = new \ReflectionClass($name);
            $methods        = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $idx => $method) {
                $workflowComponent->reset();
                $workflowComponent->setNamespace($namespace);
                $workflowComponent->setComponent($model);
                $workflowComponent->setMethod($method->name);
                $m = new \ReflectionMethod($class,$method->name);
                $c = $m->getDeclaringClass();
                if ($c->name !== $name) {
                    //this skips any methods belonging to the parent class
                    continue;
                }
                $comments               = trim($this->processDocComments($reflection->getMethod($method->name),$method));
                if ($comments) {
                    $workflowComment->reset();
                    $workflowComment->setNamespace($namespace);
                    $workflowComment->setClass($model);
                    $workflowComment->setMethod($method->name);
                    $workflowComment->setComment($comments);
                    $workflowComment->save();
                }
                $customAnnotations      = $this->processDocAnnotations($reflection->getMethod($method->name),$method);
                $authorization          = false;
                $listener               = false;
                foreach ($customAnnotations as $annotation) {
                    $clauses   = explode(' ',$annotation);
                    foreach ($clauses as $clause) {
                        $value = '';
                        if (strpos($clause,'(') && (strpos($clause,')'))) {
                            $data  = explode('(',$clause);
                            $token = $data[0];
                            $data  = explode(')',$data[1]);
                            $value = $data[0];
                        } else {
                            $token = $clause;
                        }
                        switch ($token) {
                            case "workflow"         :   //nop
                                                        break;
                            case "use"              :   $uses = explode(',',$value);
                                                        $this->output("WORKFLOW","     Registering Workflow Element ".$method->name);
                                                        foreach ($uses as $use) {
                                                            $use = 'set'.ucfirst(strtolower($use));
                                                            $workflowComponent->$use('Y');
                                                        }
                                                        break;
                            case "tags"             :
                                                        break;
                            case "listen"           :
                            case "listener"         :   $listener = true;
                                                        break;  
                            case "event"            :   if ($listener) {
                                                            $this->registerMethodListeners($namespace,$model,$method->name,$value);
                                                        }
                                                        break 2;              //this is different from a component so just skip to the next one
                            case "auth"             :
                            case "authorization"    :   if (strtolower($value) == 'true') {
                                                            $authorization = true;
                                                        } else if (strtolower($value) == 'false') {
                                                            $authorization = false;
                                                        } else {
                                                            //throw an exception and stop processing
                                                        }
                                                        $workflowComponent->setAuthorization((($authorization) ? 'Y' : 'N'));
                                                        break;
                            case "conf"             :
                            case "config"           :
                            case "cfg"              :
                            case "configuration"    :   $workflowComponent->setConfiguration($value);
                                                        $this->configurationInitializationCheck($value);
                                                        break;
                            case "namespace"        : 
                                                        break;
                            default                 :   break;
                        }
                    }
                    $workflowComponent->save();
                }
            }
        }
        return $this;
    }
}