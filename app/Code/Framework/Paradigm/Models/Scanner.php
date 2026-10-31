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
    
    private $skip       = [ 'MySQL'     => true,
                            'ORM'       => true,
                            'Postgres'  => true,
                            'SQLLite'   => true,
                            'SQLServer' => true];
	
    private $report    = false;
    private $status    = [];
    private $component = null;
    private $comment   = null;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->component  = Humble::entity('paradigm/workflow/components');
        $this->comment    = Humble::entity('paradigm/workflow/comments');        
    }

    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return system
     */
    public function className() {
        return __CLASS__;
    }

    /**
     * Scans code for workflow components
     * 
     * @param string $code
     * @param bool $report
     * @return $this
     */
    public function scan($resource=false,$report=false) {
        $this->report = $report;
        print("I am scanning $resource\n");
        return $this;
    }
    
    /**
     * Checks to see if the configuration page exists, and if not, creates the page using a template
     * 
     * @param string $uri
     */
    protected function configurationInitializationCheck($uri=false) {
        if ($uri) {
            if (count($parts  = explode('/',$uri))==3) {
                if ($module   = Humble::module($parts[0])) {
                    $mod_path = 'Code/'.$module['package'].'/'.$module['views'].'/'.$parts[1].'/Smarty/'.$parts[2].'.tpl';
                    $cnt_path = 'Code/'.$module['package'].'/'.str_replace('_','/',$module['controllers']).'/'.$parts[1].'.xml';
                    if (!file_exists($cnt_path)) {
                        $result = Humble::exec('Component','componentConfigurationTemplate',['uri' => $uri]);
                    }
                    if (!file_exists($mod_path)) {
                        $base       = Humble::module(\Environment::namespace());
                        $tpl_path   = file_exists('Code/'.$base['package'].'/'.$base['module'].'/etc/template.tpl') ? 'Code/'.$base['package'].'/'.$base['module'].'/etc/template.tpl' : 'Code/Framework/Humble/etc/template.tpl';
                        $this->copy($tpl_path,$mod_path);
                        $this->output("WORKFLOW","Creating configuration template ".$mod_path);
                    }
                }
            }
        }
    }
    
    /**
     * Find just the comment portion of the doc comment
     *
     * @param ReflectionClass $md
     * @param Object $method
     * @return string
     */
    protected function processDocComments($reflection_method=false,$public_method=false,$namespace,$model) {
        if ($reflection_method && $public_method) {
            $skip       = ['/**'=>true, '/'=>true, '*/'=>true, ''=>true, '/**/'=>true];
            $comments   = [];
            try {
                $docComments = explode("\n",$reflection_method->getDocComment());
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
            if ($comments) {
                $this->comment->reset();
                $this->comment->setNamespace($namespace);
                $this->comment->setClass($model);
                $this->comment->setMethod($public_method->name);
                $this->comment->setComment($comments);
               // $this->comment->save();
            }        
        }
        return $this;
    }
        
    /**
     * Fetches the document comment (if any) and looks for any of our custom annotations, saving those into an array, and returning that array to the calling routine
     *
     * @param ReflectionClass $md
     * @param Object $method
     * @return array
     */
    protected function processDocAnnotations($reflection_method=false) {
        $components = [];
        try {
            $annotations = explode("\n",$reflection_method->getDocComment());
            foreach ($annotations as $annotation) {
            if ((strpos($annotation,'@workflow')!==false) || (strpos($annotation,'@listen')!==false))  {
                    $components[] = substr($annotation,strpos($annotation,'@')+1);
                }
            }
        } catch (\ReflectionException $ex) {
         //  \Log::console($ex);
        }
        return $components;
    }
      
    protected function processAnnotations($reflection_method=false,$public_method=false,$namespace=false,$model=false) {
        if ($reflection_method && $public_method && $namespace && $model) {
            $customAnnotations      = $this->processDocAnnotations($reflection_method);
            $authorization          = false;
            $listener               = false;
            foreach ($customAnnotations as $annotation) {
                $this->component->reset()->setNamespace($namespace)->setComponent($model)->setMethod($public_method->name);                 
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
                                                    $this->output("WORKFLOW","     Registering Workflow Element ".$public_method->name);
                                                    foreach ($uses as $use) {
                                                        $use = 'set'.ucfirst(strtolower($use));
                                                        $this->component->$use('Y');
                                                    }
                                                    break;
                        case "tags"             :
                                                    break;
                        case "listen"           :
                        case "listener"         :   $listener = true;
                                                    break;  
                        case "event"            :   if ($listener) {
                                                        $this->registerMethodListeners($namespace,$model,$public_method->name,$value);
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
                                                    $this->component->setAuthorization((($authorization) ? 'Y' : 'N'));
                                                    break;
                        case "conf"             :
                        case "config"           :
                        case "cfg"              :
                        case "configuration"    :   $this->component->setConfiguration($value);
                                                    $this->configurationInitializationCheck($value);
                                                    break;
                        case "namespace"        : 
                                                    break;
                        default                 :   break;
                    }
                }
                //$this->component->save();
            }
        }
        return $this;
    }
    /**
     * Will search through a modules PHP components and record any that are
     * listed as being workflow components
     *
     */
    public function registerWorkflowComponents($class_name=false,$namespace=false) {
        if ($namespace = ($namespace) ? $namespace : ($this->getNamespace() ? $this->getNamespace() : false)) { 
            $model     = str_replace('.php','',$class_name);
            if (!(($namespace === 'humble') && (isset($this->skip[$model])))) {   //skips these because they will cause an error when reflection class is instantiated
                print("Scanning ".$namespace.'/'.$model."\n");
                if (method_exists($class = Humble::model($namespace.'/'.$model), 'className')) {
                    $reflection = new \ReflectionClass($class->className());
                    foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $public_method) {
                        $method            = new \ReflectionMethod($class,$public_method->name);
                        $declaring_class   = $method->getDeclaringClass();
                        $reflection_method = $reflection->getMethod($public_method->name);
                        if ($declaring_class->name !== $class_name) {
                            continue; //this skips any methods belonging to the parent class
                        }
                        $this->processDocComments($reflection_method,$public_method,$namespace,$model);
                        $this->processAnnotations($reflection_method,$public_method,$namespace,$model);
                    }
                }
            }
        }
        return $this;
    }
}