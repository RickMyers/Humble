<?php
namespace Code\Framework\Humble\Helpers;
use SimpleXMLElement;
use Humble;
use Log;
use Environment;
/**
 * Installer: installs a module.  If installing the core module, it creates the environment as well.
 *
 * Because this may be the first time (installation), we can't use the ORM, since it hasn't been setup yet...
 *  thus we use straight SQL statements
 *
 * What this does:
 *  o Clears existing setups
 *  o Runs the install SQL scripts
 *  o Copies all images over
 *  o Compiles all controllers, which is important to pick up any compiler changes
 *
 */
class Installer extends Directory
{
    protected $source             = null;
    protected $namespace          = null;
    protected $version            = null;
    protected $description        = null;
    protected $title              = null;
    protected $authorName         = null;
    protected $package            = null;
    protected $module             = null;
    protected $configuration      = null;
    protected $authorEmail        = null;
    protected $mongodb            = "";
    protected $weight             = null; //controls order of adding web components
    protected $_db                = null;
    protected $lastTime           = 0;
    protected $init               = 0;
    protected $lastStage          = '';

    /**
     *
     */
    public function __construct()    {
        parent::__construct();
        $this->_db  = Humble::connection($this);
        $project    = Environment::getProject();
        if ($project->namespace) {
            @mkdir('../../logs/'.$project->namespace,0777,true);   //added this later
        }
        $this->init = $this->lastTime = time();
    }

    public function reset() {
        return $this;
    }
    
    public function output($stage='',$message='') {
        $now = time();
        if ($stage !== $this->lastStage) {
            $this->lastStage = $stage;
            $this->lastTime  = $now;
        }
        print('['.str_pad($stage,16," ",STR_PAD_RIGHT).']['.date('H:i:s').'] '.str_pad(substr($message,0,80),80," ",STR_PAD_RIGHT)."[".str_pad($now - $this->lastTime,4,0,STR_PAD_LEFT)."][".str_pad($now-$this->init,4,0,STR_PAD_LEFT)."]\n");
        return $this;
    }
    
    /**
     *
     * @return system
     */
    public function getClassName()   {
        return __CLASS__;
    }

    /**
     * Returns the entities being managed by a module
     * 
     * @param string $prefix
     * @return iterator
     */
    public function moduleEntities($prefix=false) {
        $modules = [];
        if ($prefix = ($prefix) ?: ($this->getPrefix() ?: false)) {
            foreach ($this->_db->query("show tables like '".$prefix."%'") as $module) {
                foreach ($module as $junk => $entity) {
                    $modules[] = substr($entity,strpos($entity,'_')+1);
                }
            };
        }
        return $modules;
    }
    
    /**
     *
     * @param type $namespace
     * @param type $controller
     */
    protected function unInstallModule($namespace=false)    {
        $clause = '';
        $namespace  = ($namespace !== false) ? $namespace : $this->namespace;
        $query  = <<<SQL
            delete from humble_modules
             where namespace    = '{$namespace}'
SQL;
        $this->_db->query($query);
        return $this;
    }

    /**
     *
     * @param type $namespace
     */
    protected function unInstallEntities($namespace=false)    {
        $namespace  = ($namespace !== false) ? $namespace : $this->namespace;
        $query  = <<<SQL
            delete from humble_entities
             where namespace    = '{$namespace}'
SQL;
        $this->_db->query($query);
        $query  = <<<SQL
            delete from humble_entity_keys
             where namespace    = '{$namespace}'
SQL;
        $this->_db->query($query);
        $query  = <<<SQL
            delete from humble_entity_columns
             where namespace    = '{$namespace}'
SQL;
        $this->_db->query($query);
        return $this;
    }

    /**
     *
     * @param type $source
     * @param type $package
     */
    protected function installSchema($source=false,$module=false)   {
        if ($source) {
            $source = str_replace('_','/',('Code/'.$module->package.'/'.$source.'/'));
            if (is_dir($source)) {
                if ($handle = opendir($source)) {
                    while (false !== ($entry = readdir($handle))) {
                        if (($entry == '.') || ($entry == '..')) {
                            continue;
                        }
                        $sql = file_get_contents($source.'/'.$entry);
                        $sql = explode(';',$sql);
                        foreach ($sql as $idx => $statement) {
                            if (trim($statement)) {
                                $this->_db->query($statement);
                            }
                        }
                    }
                }
            } else {
                @mkdir($source,0775,true);
            }
        }
        return $this;
    }



    /**
     * Will store the file structure of the module that we are installing
     *
     * @param type $prefix
     * @param type $structure
     */
    protected function storeStructure($prefix,$structure,$module=false) {
        $required       = (string)$module->required;
        $use            = (string)$module->use;
        $package        = (string)$module->package;
        $models         = (string)$structure->models->source;
        $moduleName     = (string)$module->name;
        $weight         = (string)$module->weight;
        $models         = (string)$structure->models->source;
        $helpers        = (string)$structure->helpers->source;
        $controller     = (string)$structure->controllers->source;
        $configuration  = (string)$structure->configuration->source;
        $controllerCache = (string)$structure->controllers->cache;
        $views          = (string)$structure->views->source;
        $viewsCache     = (string)$structure->views->cache;
        $entities       = (string)$structure->entities->source;
        $resourcesSql   = (string)$structure->resources->sql;
        $resourcesJs    = (string)$structure->resources->js;
        $resourcesTpl   = (string)$structure->resources->templates;
        $RPC            = isset($structure->RPC) ? (string)$structure->RPC->source : "";
        $images         = $structure->images->source;
        if (is_dir('Code/'.$package.'/'.str_replace('_','/',$images))) {
            $sourceDir      = 'Code/'.$package.'/'.str_replace('_','/',$images);
            $destination    = "../images/".$this->namespace;
            @mkdir($destination,0775,true);
            $this->copyDirectory($sourceDir, $destination);
        }
        $imagesCache    = $structure->images->cache;
        $s_install      =  isset($structure->schema->install) ? (string)$structure->schema->install : '';
        $s_update       =  isset($structure->schema->update)  ? (string)$structure->schema->update : '';
        $s_layout       =  isset($structure->schema->layout)  ? (string)$structure->schema->layout : '';
        $this->unInstallModule($this->namespace);
        $enabled        = ($required === "Y") ? "Y" : "N";
        $title          = addslashes($this->title);
        $description    = addslashes($this->description);
        $installed      = date('Y-m-d H:i:s');
        $query = <<<SQL
            insert into humble_modules
                (`title`,namespace, module, package, installed, configuration, controller, `version`, description, templater, schema_install, schema_update, schema_layout, resources_js, resources_sql, resources_templates, models, prefix, mongodb, entities, controller_cache, views, views_cache, helpers, rpc_mapping, images, images_cache, enabled,required,weight)
            values
                ('{$title}','{$this->namespace}','{$moduleName}','{$package}','{$installed}','{$configuration}','{$controller}','{$this->version}','{$description}','{$use}','{$s_install}','{$s_update}','{$s_layout}','{$resourcesJs}','{$resourcesSql}','.{$resourcesTpl}.' ,'{$models}','{$prefix}','{$this->mongodb}','{$entities}','{$controllerCache}','{$views}','{$viewsCache}','{$helpers}','{$RPC}','{$images}','{$imagesCache}','{$enabled}','{$required}','{$weight}')
SQL;
        if (!$this->_db->query($query)) {
            print('Errored on'."\n\n".$query."\n\n");
        };
        return $this;
    }

    /**
     * Regenerates the controllers from their XML, picking up any changes that might have occurred in the compiler
     */
    public function compileControllers($namespace=false) {
        $namespace  = $namespace ? $namespace : $this->namespace;
        $module     = \Humble::module($namespace);
        $controller = $module['controllers'];
        $source     = $module['package'].'/'.str_replace('_','/',$controller);
        $dest       = $source.'/Cache';
        $files      = \Humble::helper('humble/directory')->listDirectory('Code/'.$source,false); //this is weird... where do I prepend 'Code' to the name already?
        $compiler   = \Environment::getCompiler();
        $compiler->setSource($source);
        $compiler->setDestination($dest);
        foreach ($files as $file) {
            if (strpos($file,'.xml')!==false) {
                $this->output("CONTROLLERS","Compiling: ".$file);
                $compiler->compile($namespace.'/'.substr($file,0,strpos($file,'.xml')));
            }
        }
        return $this;
    }

    /**
     * Removes all named events related to the namespace being installed
     */
    protected function deRegisterEvents() {
        $query = <<<SQL
            delete from paradigm_events
             where namespace = '{$this->namespace}'
SQL;
        $this->_db->query($query);
        return $this;
    }

    /**
     * Registers all of events, and their comments, from the events section of the configuration file
     */
    protected function registerEvents($event_node = false) {
        $this->deRegisterEvents();
        if ($event_node) {
            foreach ($event_node as $events) {
                foreach ($events as $event => $data) {
                    $event_comment = addslashes($data->attributes()->comment);
                    $query = <<<SQL
                        insert into paradigm_events
                            (namespace,event,comment)
                        values
                            ('{$this->namespace}','{$event}','{$event_comment}')
SQL;
                    $this->_db->query($query);
                }
            }
        }
        return $this;
    }

    
    protected function deRegisterWebHooks() {
        $query = <<<SQL
            delete from paradigm_webhooks
             where namespace = '{$this->namespace}'
SQL;
        $this->_db->query($query);
        return $this;
    }
    
    protected function registerWebHooks($hook_node = false) {
        $this->deRegisterWebHooks();
        if ($hook_node) {
            
        }
        return $this;
    }
    
    /**
     *
     * @param type $prefix
     * @param type $structure
     */
    protected function installImages($prefix,$structure,$module=false)    {
        $images     = $structure->images->source;
        if (is_dir('Code/'.$module->package.'/'.str_replace('_','/',$images))) {
            $sourceDir      = 'Code/'.$module->package.'/'.str_replace('_','/',$images);
            $destination    = "../images/".$this->namespace;
            @mkdir($destination,0775,true);
            $this->copyDirectory($sourceDir, $destination);
        }
        return $this;
    }

   /**
     *
     *  Will delete the folder containing any custom images
     *
     */
    protected function unInstallImages($location)   {
        return $this;
    }

    protected function deRegisterListeners($namespace) {
        $namespace = ($namespace) ? $namespace : (($this->namespace) ? $this->namespace : null);
        $listeners = Humble::entity('paradigm/method/listeners');
        $listeners->setNamespace($namespace);
        $listeners->delete(true);
        return $this;
    }
    
   /**
     *
     *  Will delete the current crop of workflow components associated to a module
     *
     */
    protected function deRegisterWorkflowComponents($namespace=false) {
        $namespace = ($namespace) ? $namespace : (($this->namespace) ? $this->namespace : null);
        $components = Humble::entity('paradigm/workflow/components');
        $components->setNamespace($namespace);
        $components->delete(true);
        return $this;
    }

    public function generateWorkflows($namespace=false) {
        @mkdir('Workflows',0775);
        $this->output('WORKFLOWS','Generating workflows for Namespace '.$namespace);
        if ($namespace) {
            $generator = \Humble::helper('paradigm/generator');
            foreach (\Humble::entity('paradigm/workflows')->setNamespace($namespace)->setActive('Y')->fetch() as $workflow) {
                $this->output('WORKFLOWS',"     Generating: ".$workflow['title']);
                $generator->setId($workflow['id'])->setWorkflow($workflow['workflow'])->generate();
            }
        }
        $this->output('WORKFLOWS','Done Generating workflows');
        return $this;
    }
    
    /**
     * Find just the comment portion of the doc comment
     *
     * @param ReflectionClass $md
     * @param Object $method
     * @return string
     */
    private function processDocComments($md=false,$method=false) {
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
    private function processDocAnnotations($md=false,$method=false) {
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
     * 
     * 
     * @param string $namespace
     * @return $this
     */
    public function registerEntities($namespace=false) {
        $namespace = $namespace ?: ($this->getNamespace() ?: false);
        if ($namespace) {
            $module = Humble::module($namespace);
            if (file_exists($config = 'Code'.DIRECTORY_SEPARATOR.$module['package'].DIRECTORY_SEPARATOR.$module['configuration'].DIRECTORY_SEPARATOR.'config.xml')) {
                $xml = simplexml_load_file($config);
                foreach ($this->moduleEntities($prefix = $namespace.'_') as $entity) {
                    if (!isset($xml->{$namespace}->orm->entities->{$entity})) {
                        $xml->{$namespace}->orm->entities->addChild($entity);
                    }
                 }
                 file_put_contents($config,$xml->asXML());
            }
        }
        return $this;
    }
    
    public function registerMethodListeners($namespace,$class,$listener,$events) {
        $method_listener = Humble::entity('paradigm/method/listeners');
        foreach (explode(',',$events) as $event) {
            $this->output("WORKFLOW","     Registering Method Trigger ".$event." on ".$listener);
            $method_listener->reset()->setNamespace($namespace)->setClass($class)->setMethod($listener)->setEvent($event)->save();
        }
        return $this;
    }
    
    /**
     * Checks to see if the configuration page exists, and if not, creates the page using a template
     * 
     * @param string $uri
     */
    public function configurationInitializationCheck($uri=false) {
        if ($uri) {
            if (count($parts  = explode('/',$uri))==3) {
                if ($module   = Humble::module($parts[0])) {
                    $mod_path = 'Code/'.$module['package'].'/'.$module['views'].'/'.$parts[1].'/Smarty/'.$parts[2].'.tpl';
                    if (!file_exists($mod_path)) {
                        $base       = Humble::module(\Environment::namespace());
                        $tpl_path   = file_exists('Code/'.$base['package'].'/'.$base['module'].'/etc/template.tpl') ? 'Code/'.$base['package'].'/'.$base['module'].'/etc/template.tpl' : 'Code/Framework/Humble/etc/template.tpl';
                        copy($tpl_path,$mod_path);
                        $this->output("WORKFLOW","Creating configuration template ".$mod_path);
                    }
                }
            }
        }
    }
    
    /**
     * Will search through a modules PHP components and record any that are
     * listed as being workflow components
     *
     */
    public function registerWorkflowComponents($namespace=false) {
        $namespace  = ($namespace) ? $namespace : (($this->namespace) ? $this->namespace : null);
        if ($namespace) {
            $this->deRegisterWorkflowComponents($namespace);            
            $this->deRegisterListeners($namespace);
        }
        $models             = Humble::getModels($namespace);
        $workflowComponent  = Humble::entity('paradigm/workflow/components');
        $workflowComment    = Humble::entity('paradigm/workflow/comments');
        $this->output("WORKFLOW","Processing Namespace [".$namespace."]...");
        foreach ($models as $model) { 
            $this->output("WORKFLOW","");
            $this->output("WORKFLOW","Scanning Model Class ".ucfirst($model)."...");
            $workflowComponent->setNamespace($namespace);
            $workflowComponent->setComponent($model);
            $class          = Humble::model($namespace.'/'.$model);
            if (!method_exists($class, 'getClassName')) {
                //$this->output("",$model);
                continue;
            }
            $name           = $class->getClassName();
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
                    //print_r($clauses);die();
                    foreach ($clauses as $clause) {
                        //print($clause."\n");
                        $value = '';
                        if (strpos($clause,'(') && (strpos($clause,')'))) {
                            $data  = explode('(',$clause);
                            $token = $data[0];
                            $data  = explode(')',$data[1]);
                            $value = $data[0];
                        } else {
                            $token = $clause;
                        }
                        //print($token."\n");
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
                            default                 :   break;
                        }
                    }
                    $workflowComponent->save();
                }
            }
            $this->output("WORKFLOW","Finished Scanning ".ucfirst($model)."...");
        }
        return $this;
    }

    /**
     * @WTF:  What am I doing here?  What was my plan???
     * @param type $where
     */
    public function moveFrontEnd($where) {
        $destination = 'web/clients/'.$this->namespace;
        if (!is_dir($destination)) {
            @mkdir($destination,0775,true);
        }
        $source = str_replace('_','/','Code_'.$this->package.'_'.$where);
        if (is_dir($source)) {
            $this->copyDirectory($source,$destination);
        }
        return $this;
    }

    /**
     *
     */
    protected function storeEntities($orm,$prefix=false)    {
      $environment  = \Singleton::getEnvironment();
      $prefix       = ($prefix) ? $prefix : $orm->prefix;
      $this->unInstallEntities($this->namespace);
      foreach ($orm->entities as $name => $entities) {
          foreach ($entities as $name => $entity) {
            $this->output('ENTITIES','Registering '.$name);
            //first save off key field data
            $data     = $entity->attributes();
            $polyglot = isset($data['polyglot']) ? $data['polyglot'] : 'N';
            $actual   = isset($data['actual'])   ? $data['actual']   : null;
            $alias    = isset($data['alias'])    ? $data['alias']    : '';
            $query    = <<<SQL
                    insert into humble_entities
                        (namespace, entity, actual, polyglot, `alias`)
                    values
                        ('{$this->namespace}','{$name}','{$actual}','{$polyglot}','{$alias}')
SQL;
            $this->_db->query($query);
            $table     = ($actual) ? $actual : $prefix.$name;
            $query     = <<<SQL
              show keys in {$table} where key_name = 'PRIMARY'
SQL;
            $results   = $this->_db->query($query);
            foreach ($results as $rKey => $row) {
                $col   = $row['Column_name'];
                $query = <<<SQL
                        SELECT extra FROM  information_schema.COLUMNS WHERE table_schema = '{$environment->getDatabase()}' AND TABLE_NAME = '{$table}' AND column_name = '{$col}'
SQL;
                $data  = $this->_db->query($query);
                $inc   = 'N';
                if (isset($data[0]['extra'])) {
                    $inc = ($data[0]['extra']=="auto_increment") ? 'Y' : 'N';
                }
                $query = <<<SQL
                       insert into humble_entity_keys
                          (namespace,entity,`key`,auto_inc)
                       values
                          ('{$this->namespace}','{$name}','{$col}','{$inc}')
SQL;
                    $this->_db->query($query);
                }
                //now get a list of columns that are non-key.  If you try to save a field that isn't in this list, it will get redirected to a mongodb collection
                $query = <<<SQL
                    SHOW COLUMNS IN {$table} WHERE `Key` != 'PRI'
SQL;
                $results = $this->_db->query($query);
                if ($results) {
                    foreach ($results as $row) {
                        $query = <<<SQL
                             insert into humble_entity_columns
                                (namespace, entity, `column`)
                             values
                                ('{$this->namespace}','{$name}','{$row['Field']}')
SQL;
                        $this->_db->query($query);
                    }
                }
                if ($e = \Humble::entity($this->namespace.'/'.$name)) {
                    $e->recache();
                }
            }
        }
        return $this;
    }

    /**
     *
     *                         $yaml .= "  ".$column['Field'].":\n";
                        $yaml .= "      Type: ".$column['Type']."\n";
                        $yaml .= "      Null: ".$column['Null']."\n";
                        $yaml .= "      Key: ".$column['Key']."\n";
                        $yaml .= "      Default: ".$column['Default']."\n";
                        $yaml .= "      Extra: ".$column['Extra']."\n";
                        $yaml .= "      Required: false\n";
                        $yaml .= "      MinLength: 0\n";
                        $yaml .= "      Format:  [regular expression]\n";
     *
     */
    public function generateLayoutSchema($package, $namespace, $source='',$entities=[],$force=false) {
        $destinationDirectory = str_replace('_','/','Code/'.$package.'/'.$source);
        @mkdir($destinationDirectory,0775,true);
        foreach ($entities as $idx => $entity) {
            foreach ($entity as $name => $e) {
                $layoutFile = $destinationDirectory.'/'.$name.'.json';
                if (!file_exists($layoutFile) || $force) {
                    $stamp = chr(rand(ord('A'), ord('Z')));
                    for ($i=0; $i<5; $i++) {
                        $stamp .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('A'), ord('Z')));
                    }
                    $table  = Humble::entity($namespace.'/'.$name);
                    $data   = $table->describe();
                    $fields = '';
                    foreach ($data as $idx => $column) {
                        if ($fields) {
                            $fields .= ",\n";
                        }
                        $fields .= <<<FIELD
            {
                "active":       true,
                "id": 			"{$column['Field']}_{$stamp}",
                "name":         "{$column['Field']}",
                "longname":		"{$column['Field']}",
                "title":		"{$column['Field']} rollover",
                "type":         "text",
                "required":		true,
                "force":		true,
                "classname":	"",
                "style":		""
            }
FIELD;
                    }
                    $json   = str_replace(['&&FORM&&','&&FIELDS&&','&&FORM_ID&&'],['default-'.$name.'-form',$fields,$stamp],file_get_contents('Code/Framework/Humble/lib/sample/component/DSL.json'));
                    file_put_contents($layoutFile,$json);
                }
            }
        }
        return $this;
    }

    /**
     *
     */
    protected function deRegisterWebComponents($namespace,$package=false)    {
        $clause = '';
        if ($package) {
            $clause = "and package   = '{$package}'";
        }
        $query = <<<SQL
            delete from humble_js
             where namespace = '{$namespace}'
               {$clause}
SQL;
        $this->_db->query($query); //remove JS
        $query = <<<SQL
            delete from humble_css
             where namespace = '{$namespace}'
               {$clause}
SQL;
        $this->_db->query($query); //remove CSS
        $query = <<<SQL
            delete from humble_edits
             where namespace = '{$namespace}'
SQL;
        $this->_db->query($query); 
        return $this;
    }

    /**
     *
     */
    protected function registerWebComponent($type,$package,$namespace,$weight,$file)    {
        $query = <<<SQL
            insert into humble_{$type}
                (namespace,package,source,weight)
            values
                ('{$namespace}','{$package}','{$file}','{$weight}')
SQL;
        $this->_db->query($query);
        return $this;
    }

    /**
     *
     */
    protected function registerWebEdit($namespace,$form,$source)    {
        $query = <<<SQL
            insert into humble_edits
                (namespace,form,source)
            values
                ('{$namespace}','{$form}','{$source}')
SQL;
        $this->_db->query($query);
        return $this;
    }

    /**
     *
     */
    protected function registerWebEdits($web)    {
        if (isset($web->edits)) {
            foreach ($web->edits as $node => $edits) {
                foreach ($edits as $form => $editFile) {
                    $this->registerWebEdit($this->namespace,$form,str_replace('_','/',$editFile));
                }
            }
        }
        return $this;
    }

    /**
     *
     */
    public function registerWebComponents($web,$module=false)
    {
        $this->output('WEB','Registering Web Components');
        foreach ($web as $node => $items) {
            $this->deRegisterWebComponents($this->namespace,false);            
            foreach ($items as $package => $item) {
                if (isset($item->js)) {
                    foreach ($item->js->source as $jsFile) {
                        if ((string)$jsFile) {
                            $weight = (isset($jsFile['weight'])) ? (int)$jsFile['weight'] : $this->weight ;
                            $this->registerWebComponent('js',$package,$this->namespace,$weight,str_replace('_','/',$jsFile));
                        }
                    }
                }
                if (isset($item->css)) {
                    foreach ($item->css->source as $cssFile) {
                        if ((string)$cssFile) {
                            $weight = (isset($cssFile['weight'])) ? (int)$cssFile['weight'] : $this->weight ;
                            $this->registerWebComponent('css',$package,$this->namespace,$weight,str_replace('_','/',$cssFile));
                        }
                    }
                }
            }
        }
        $this->registerWebEdits($web);
        $this->output('WEB','Done Registering Web Components');
        return $this;
    }

    /**
     *
     */
    public function uninstall($source=false) {
        $source = ($source!==false) ? $source : $this->getSource();
        $helper = Humble::helper('humble/data');
        if (file_exists($source)) {
          //  \Log::console('Starting uninstallation of: '.$source);
            if ($helper->isValidXML($xml = file_get_contents($source))) {
                $xml    = new SimpleXMLElement($xml);
                foreach ($xml as $namespace => $contents) {
                    $this->unInstallModule($namespace);
                    $this->unInstallEntities($namespace);
                    $this->deRegisterWebComponents($namespace, false);
                }
            }
        } else {
          //  \Log::console('Could not find source file for uninstallation: '.$source);
        }
        return $this;
    }

    /**
     * Call this when the source was deleted before going through a proper uninstallation of a module
     */
    public function deregister($namespace=false) {
        $status = false;
        if ($namespace) {
            $status = true;
            $this->unInstallModule($namespace);
            $this->unInstallEntities($namespace);
            $this->deRegisterWebComponents($namespace, false);
        }
        return $status;
    }

    /**
     *
     */
    public function install($source=false)  {
        $source = ($source!==false) ? $source : $this->getSource();
        if (file_exists($source)) {
            if ($xml = new SimpleXMLElement(file_get_contents($source))) {
                foreach ($xml as $namespace => $contents) {
                    Humble::cache('module-'.$namespace,null);                   //removes any data we might have had in the cache
                    $this->namespace    = $namespace;
                    $this->title        = $contents->title;
                    $this->version      = $contents->version;
                    $this->description  = $contents->description;
                    $this->authorName   = $contents->author->name;
                    $this->authorEmail  = $contents->author->email;
                    $this->weight       =  (isset($contents->weight)) ? $contents->weight : 99;
                    $this->package      = $contents->module->package;
                    $base               = 'Code/'.$this->package.'/';
                    foreach ($contents->structure as $what => $data) {
                        foreach ($data as $file) {
                            if (isset($file->source)) {
                                @mkdir($base.str_replace('_','/',$file->source),0775,true);
                                if (isset($file->cache)) {
                                    @mkdir($base.str_replace('_','/',$file->cache),0775,true);
                                }
                            }
                        }
                    }
                    if (isset($contents->structure->schema) && (isset($contents->structure->schema->update))) {
                        $this->installSchema($contents->structure->schema->install,$contents->module);
                    }
                    if (isset($contents->orm)){
                        $this->mongodb      = isset($contents->orm->mongodb) ? $contents->orm->mongodb : "";
                        $this->prefix       = $contents->orm->prefix;
                        $this->storeEntities($contents->orm,$this->prefix);
                    }
                    if (isset($contents->structure)) {
                        $this->storeStructure($this->prefix,$contents->structure,$contents->module);
                        if (isset($contents->module->workflow) && ($contents->module->workflow==='Y')) {
                            $this->registerWorkflowComponents($this->namespace,$contents->module);
                        }
                    }
                    if (isset($contents->structure->schema) && (isset($contents->structure->schema->layout))) {
                      //  $this->generateLayoutSchema($this->package,$this->namespace,$contents->structure->schema->layout,$contents->orm->entities,$contents->module);
                    }
                    if (isset($contents->structure->images)) {
                        $this->installImages($this->prefix,$contents->structure,$contents->module);
                    }
                //  if (isset($contents->events)) {
                //     $this->registerEvents($contents->events);  //can't do this on install, since paradigm won't exist yet.  after install, run update
                //  }
                    if (isset($contents->web)) {
                        $this->registerWebComponents($contents->web,$contents->module);
                    }
                  /*  if (isset($contents->structure->frontend)) {
                        $this->moveFrontEnd($contents->structure->frontend->source,$contents->module);
                    }*/
                    $install_file  = "Code\\".(string)$contents->module->package."\\".str_replace(["_","/"],["\\","\\"],(string)$contents->structure->models->source)."\\OnInstall.php";
                    $install_class = "Code\\".(string)$contents->module->package."\\".str_replace(["_","/"],["\\","\\"],(string)$contents->structure->models->source)."\\OnInstall";
                    if (file_exists($install_file) && class_exists($install_class)) {
                        $i = Humble::model($namespace.'/OnInstall',true)->execute();
                    }
                    Humble::cache('module-'.$namespace,Humble::module($namespace));
                }
                
            } else {
                foreach ($helper->getErrors() as $error) {
                    $this->output('ERRORS',$error);
                }
            }
            Environment::recacheApplication();
        } else {
            \Log::console('Could not find source file for installation: '.$source);
        }
        return $xml;
    }

    /**
     * Will turn installation off after a successful run...
     */
    public function disable() {
        $xml = Environment::applicationXML();
        $xml->status->installer = 0;
        file_put_contents(Environment::applicationXMLLocation(),$xml->asXML());
        return $this;
    }

    /**
     *
     */
    public function getModules()   {
        $modules = [];
        $root    = "Code/";
        $entries = dir($root);
        $helper  = Humble::helper('humble/data');
        while (($entry = $entries->read()) !== false) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            } else {
                $file = $root.$entry;
                if (is_dir($file)) {
                    $modules[$entry] = [];
                    $candidates = dir($file);
                    while (($candidate = $candidates->read()) !== false) {
                        if (($candidate == '.') || ($candidate == '..')) {
                            continue;
                        } else {

                            $modfile    = $file.'/'.$candidate;
                            $etcfile    = $modfile.'/etc/config.xml';
                            if ((is_dir($modfile)) && (file_exists($etcfile))) {
                                $modules[$entry][$candidate] = [];
                                if ($helper->isValidXML($xml = file_get_contents($etcfile))) {
                                    $xml    = new SimpleXMLElement($xml);
                                    foreach ($xml as $namespace => $contents) {
                                        $modules[$entry][$candidate]['namespace']       = $namespace;
                                        $modules[$entry][$candidate]['title']           = $contents->title;
                                        $modules[$entry][$candidate]['version']         = $contents->version;
                                        $modules[$entry][$candidate]['description']     = $contents->description;
                                        $modules[$entry][$candidate]['author']          = $contents->author->name;
                                        $modules[$entry][$candidate]['email']           = $contents->author->email;
                                        $modules[$entry][$candidate]['weight']          = (isset($contents->weight)) ? $contents->weight : 'N/A';
                                        if (isset($contents->orm)){
                                            $modules[$entry][$candidate]['prefix']      = $contents->orm->prefix;
                                            $modules[$entry][$candidate]['mongodb']     = (isset($contents->orm->mongodb)) ? $contents->orm->mongodb : '' ;
                                            //$modules[$entry][$candidate]['entitylist']= $contents->orm->entities;
                                        } else {
                                            $modules[$entry][$candidate]['prefix']      = '';
                                            $modules[$entry][$candidate]['mongodb']     = '';
                                        }
                                        if (isset($contents->module)) {
                                            $modules[$entry][$candidate]['module']      = $contents->module->name;
                                            $modules[$entry][$candidate]['required']    = $contents->module->required;
                                            $modules[$entry][$candidate]['templater']   = $contents->module->use;
                                            $modules[$entry][$candidate]['workflow']   = $contents->module->workflow;
                                            $modules[$entry][$candidate]['weight']   = $contents->module->weight;
                                            $modules[$entry][$candidate]['package']     = $contents->module->package;
                                        }
                                        if (isset($contents->structure)) {
                                            $modules[$entry][$candidate]['configuration'] = $contents->structure->configuration->source;
                                            $modules[$entry][$candidate]['models']      = $contents->structure->models->source;
                                            $modules[$entry][$candidate]['controller']  = $contents->structure->controllers->source;

                                            $modules[$entry][$candidate]['RPC']         = $contents->structure->RPC->source;
                                            $modules[$entry][$candidate]['schema_install']   = $contents->structure->schema->install;
                                            $modules[$entry][$candidate]['schema_update']    = $contents->structure->schema->update;
                                            $modules[$entry][$candidate]['schema_layout']    = $contents->structure->schema->layout;
                                            $modules[$entry][$candidate]['controller_cache'] = $contents->structure->controllers->cache;
                                            $modules[$entry][$candidate]['views']       = $contents->structure->views->source;
                                            $modules[$entry][$candidate]['images']      = $contents->structure->images->source;
                                            $modules[$entry][$candidate]['helpers']     = $contents->structure->helpers->source;
                                            $modules[$entry][$candidate]['views_cache'] = $contents->structure->views->cache;
                                            $modules[$entry][$candidate]['entities']    = $contents->structure->entities->source;
                                        }
                                        if (isset($contents->application)) {
                                        }
                                        if (isset($contents->web)) {
                                        }
                                    }
                                } else {
                                    $this->output("ERROR",'Configuration Not Valid '.$etcfile);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $modules;
    }

    /**
     *
     */
    public function setSource($arg)             { $this->source         = $arg; }

    /**
     *
     */
    public function getSource()                 { return $this->source;         }
}
