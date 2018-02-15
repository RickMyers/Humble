<?php
namespace Code\Base\Humble\Helpers;
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

    /**
     *
     */
    public function __construct()    {
        parent::__construct();
        $this->_db  = Humble::getDatabaseConnection($this);
        $project    = Environment::getProject();
        if ($project->namespace) {
            @mkdir('../../logs/'.$project->namespace,0777,true);   //added this later
        }
    }

    /**
     *
     * @return system
     */
    public function getClassName()   {
        return __CLASS__;
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
                (`title`,namespace, module, package, installed, configuration, controller, `version`, description, templater, schema_install, schema_update, schema_layout, models, prefix, mongodb, entities, controller_cache, views, views_cache, helpers, rpc_mapping, images, images_cache, enabled,required,weight)
            values
                ('{$title}','{$this->namespace}','{$moduleName}','{$package}','{$installed}','{$configuration}','{$controller}','{$this->version}','{$description}','{$use}','{$s_install}','{$s_update}','{$s_layout}','{$models}','{$prefix}','{$this->mongodb}','{$entities}','{$controllerCache}','{$views}','{$viewsCache}','{$helpers}','{$RPC}','{$images}','{$imagesCache}','{$enabled}','{$required}','{$weight}')
SQL;
        $this->_db->query($query);
    }

    /**
     * Regenerates the controllers from their XML, picking up any changes that might have occurred in the compiler
     */
    public function compileControllers($namespace=false) {
        $namespace  = $namespace ? $namespace : $this->namespace;
        $module     = \Humble::getModule($namespace);
        $controller = $module['controller'];
        $source     = $module['package'].'/'.str_replace('_','/',$controller);
        $dest       = $source.'/Cache';
        $files      = \Humble::getHelper('core/directory')->listDirectory('Code/'.$source,false); //this is weird... where do I prepend 'Code' to the name already?
        $compiler   = \Environment::getCompiler();
        $compiler->setSource($source);
        $compiler->setDestination($dest);
        foreach ($files as $file) {
            if (strpos($file,'.xml')!==false) {
                print("Compiling: ".$file."\n");
                $compiler->compile($namespace.'/'.substr($file,0,strpos($file,'.xml')));
            }
        }
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
    }

    /**
     * Registers all of events, and their comments, from the events section of the configuration file
     */
    protected function registerEvents($event_node = false) {
        $this->deRegisterEvents();
        if ($event_node!==false) {
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
    }

   /**
     *
     *  Will delete the folder containing any custom images
     *
     */
    protected function unInstallImages($location)   {
    }

   /**
     *
     *  Will delete the current crop of workflow components associated to a module
     *
     */
    protected function deRegisterWorkflowComponents($namespace=false) {
        $namespace = ($namespace) ? $namespace : (($this->namespace) ? $this->namespace : null);
        $components = Humble::getEntity('paradigm/workflow_components');
        $components->setNamespace($namespace);
        $components->delete();
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
                if (strpos($comment,'@workflow')!==false) {
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
    protected function registerWorkflowComponents($namespace=false) {
        $namespace  = ($namespace) ? $namespace : (($this->namespace) ? $this->namespace : null);
        $models     = Humble::getModels($namespace);
        $workflowComponent  = Humble::getEntity('paradigm/workflow_components');
        $workflowComment   = Humble::getEntity('paradigm/workflow_comments');
        print("Processing Namespace [".$namespace."]...\n\n");
        foreach ($models as $model) {
            print("\n\tScanning Model Class ".ucfirst($model)."...\n");
            $workflowComponent->setNamespace($namespace);
            $workflowComponent->setComponent($model);
            $class          = Humble::getModel($namespace.'/'.$model);
            if (!method_exists($class, 'getClassName')) {
                //print($model."\n");
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
                $authorization = false;
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
                                                        print("\t\tRegistering Workflow Element ".$method->name."\n");
                                                        foreach ($uses as $use) {
                                                            $use = 'set'.ucfirst(strtolower($use));
                                                            $workflowComponent->$use('Y');
                                                        }
                                                        break;
                            case "tags"             :
                                                        break;
                            case "authorization"    :   if (strtolower($value) == 'true') {
                                                            $authorization = true;
                                                        } else if (strtolower($value) == 'false') {
                                                            $authorization = false;
                                                        } else {
                                                            //throw an exception and stop processing
                                                        }
                                                        $workflowComponent->setAuthorization((($authorization) ? 'Y' : 'N'));
                                                        break;
                            case "configuration"    :   $workflowComponent->setConfiguration($value);
                                                        break;
                            default                 :   break;
                        }
                    }
                    $workflowComponent->save();
                }
            }
        }
    }

    /**
     *
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
    }

    /**
     *
     */
    protected function storeEntities($orm,$prefix)    {
      $environment  = \Singleton::getEnvironment();
      $prefix       = $orm->prefix;
      $this->unInstallEntities($this->namespace);
      foreach ($orm->entities as $name => $entities) {
          foreach ($entities as $name => $entity) {
            //first save off key field data
            $data = $entity->attributes();
            $polyglot = isset($data['polyglot']) ? $data['polyglot'] : 'N';
            $query = <<<SQL
                    insert into humble_entities
                        (namespace, entity, polyglot)
                    values
                        ('{$this->namespace}','{$name}','{$polyglot}')
SQL;
            $this->_db->query($query);
            $query = <<<SQL
              show keys in {$prefix}{$name} where key_name = 'PRIMARY'
SQL;
            $results = $this->_db->query($query);
            foreach ($results as $rKey => $row) {
                $col = $row['Column_name'];
                $query = <<<SQL
                        SELECT extra FROM  information_schema.COLUMNS WHERE table_schema = '{$environment->getDatabase()}' AND TABLE_NAME = '{$prefix}{$name}' AND column_name = '{$col}'
SQL;
                $data = $this->_db->query($query);
                $inc = ($data[0]['extra']=="auto_increment") ? 'Y' : 'N';
                $query = <<<SQL
                       insert into humble_entity_keys
                          (namespace,entity,`key`,auto_inc)
                       values
                          ('{$this->namespace}','{$name}','{$col}','{$inc}')
SQL;
                    $this->_db->query($query);
                }
                //now get a list of columns that are non-key.  If you try to save a field that isn't in this list, it will get redirected to a mongo db entry
                $query = <<<SQL
                    SHOW COLUMNS IN {$prefix}{$name} WHERE `Key` != 'PRI'
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
                if ($e = \Humble::getEntity($this->namespace.'/'.$name)) {
                        $e->recache();
                }
            }
        }
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
                    $table  = Humble::getEntity($namespace.'/'.$name);
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
                    $json   = str_replace(['&&FORM&&','&&FIELDS&&','&&FORM_ID&&'],['default-'.$name.'-form',$fields,$stamp],file_get_contents('Code/Base/Humble/lib/sample/component/DSL.json'));
                    file_put_contents($layoutFile,$json);
                }
            }
        }
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
        $this->_db->query($query); //remove Edits
        $query = <<<SQL
            delete from humble_pages
             where namespace = '{$namespace}'
SQL;
        $this->_db->query($query); //remove Static web pages
        $query = <<<SQL
            delete from humble_templates
             where namespace = '{$namespace}'
SQL;
        $this->_db->query($query); //remove JS Templates
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
    }

    /**
     *
     */
    protected function registerWebPage($namespace,$page,$source)    {
        $query = <<<SQL
            insert into humble_pages
                (namespace,page,source)
            values
                ('{$namespace}','{$page}','{$source}')
SQL;
        $this->_db->query($query);
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
    }

    /**
     *
     */
    protected function registerJsTemplates($web)    {
        if (isset($web->templates)) {
            foreach ($web->templates as $node => $template) {
                foreach ($template as $resource => $source) {
                    $this->registerJsTemplate($this->namespace,$resource,str_replace('_','/',$source));
                }
            }
        }
    }

    /**
     *
     */
    protected function registerWebPages($web)
    {
        if (isset($web->pages)) {
            foreach ($web->pages as $node => $pages) {
                foreach ($pages as $page => $webPage) {
                    $this->registerWebPage($this->namespace,$page,str_replace('_','/',$webPage));
                }
            }
        }
    }

    /**
     *
     */
    public function registerWebComponents($web,$module=false)
    {
        foreach ($web as $node => $items) {
            foreach ($items as $package => $item) {
                if (($package === 'edits') || ($package === "routes") || ($package=="templates")) {
                    continue;
                }
                $this->deRegisterWebComponents($this->namespace,$package);
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
        $this->registerWebPages($web);
    }

    /**
     *
     */
    public function uninstall($source=false) {
        $source = ($source!==false) ? $source : $this->getSource();
        $helper = Humble::getHelper('core/data');
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
        $helper = Humble::getHelper('core/data');
        if (file_exists($source)) {
            //if ($helper->isValidXML($xml = file_get_contents($source))) {
            if ($xml = file_get_contents($source)) {
                $xml    = new SimpleXMLElement($xml);
                foreach ($xml as $namespace => $contents) {
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
                            $this->deRegisterWorkflowComponents($this->namespace);
                            $this->registerWorkflowComponents($this->namespace,$contents->module);
                        }
                    }
                    if (isset($contents->structure->schema) && (isset($contents->structure->schema->layout))) {
                      //  $this->generateLayoutSchema($this->package,$this->namespace,$contents->structure->schema->layout,$contents->orm->entities,$contents->module);
                    }
                    if (isset($contents->structure->images)) {
                        $this->installImages($this->prefix,$contents->structure,$contents->module);
                    }
                    if (isset($contents->events)) {
                        $this->registerEvents($contents->events);
                    }
                    if (isset($contents->web)) {
                        $this->registerWebComponents($contents->web,$contents->module);
                    }
                    if (isset($contents->structure->frontend)) {
                        $this->moveFrontEnd($contents->structure->frontend->source,$contents->module);
                    }
                    $install_file  = "Code\\".(string)$contents->module->package."\\".str_replace(["_","/"],["\\","\\"],(string)$contents->structure->models->source)."\\OnInstall.php";
                    $install_class = "Code\\".(string)$contents->module->package."\\".str_replace(["_","/"],["\\","\\"],(string)$contents->structure->models->source)."\\OnInstall";
                    if (file_exists($install_file) && class_exists($install_class)) {
                        $i = Humble::getModel($namespace.'/OnInstall')->execute();
                    }
                }
            } else {
                print_r($helper->getErrors());
              //  \Log::console($helper->getErrors());
            }
            //now set install time
           // \Log::console('Did the installation for: '.$source);
        } else {
          //  \Log::console('Could not find source file for installation: '.$source);
        }
        return $xml;
    }

    /**
     *
     */
    public function getModules()   {
        $modules = [];
        $root    = "Code/";
        $entries = dir($root);
        $helper  = Humble::getHelper('core/data');
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
                                    print('not valid '.$etcfile."\n");
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
?>