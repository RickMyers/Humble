<?php
namespace Code\Framework\Humble\Helpers;
use SimpleXMLElement;
use Humble;
/*
 * Updates the environment.
 *
 * What this does:
 *  o Clears setup and reinstalls structure
 *  o Checks for any SQL Updates, runs those
 *  o Copies any images that aren't already deployed
 *  o Caches stuff
 */
class Updater extends Installer
{

    public function __construct()   {
        parent::__construct();
    }

    /**
     *
     * @return system
     */
    public function getClassName()
    {
        return __CLASS__;
    }

    /**
     *
     * @param type $source
     */
    public function install($source = false) {
        //nulling these out so can't be used
    }

    /**
     *
     * @param type $source
     */
    public function refresh($source = false) {
        //nulling these out so can't be used
    }
    
    private function extractParameterOptions($attr) {
        $options = [];
        foreach ($attr as $key => $value) {
            $options[(string)$key] = (string)$value;
        }
        return $options;
    }
    
    /**
     * We are collecting only certain descriptive elements of the controller to register with the service directory
     * 
     * @param type $action
     * @param type $elements
     * @return type
     */    
    private function extractElements($action,$elements) {
        foreach ($action as $node => $subelements) {
            switch ((string)$node) {
                case 'description'  :
                    $elements['description'] = (string)$subelements ? (string)$subelements : 'N/A';
                    break;
                case 'model'        :
                    $attr = $subelements->attributes();
                    if (!isset($attr->namespace)) {
                        $attr->namespace = \Environment::namespace();
                    }
                    $elements['models'][str_replace('_','/',(string)$attr->namespace.'/'.(string)$attr->class)] = true;
                    break;
                case 'helper'       :
                    $attr = $subelements->attributes();
                    if (!isset($attr->namespace)) {
                        $attr->namespace = \Environment::namespace();
                    }
                    $elements['helpers'][str_replace('_','/',(string)$attr->namespace.'/'.(string)$attr->class)] = true;
                    break;
                case 'entity'       :
                    $attr = $subelements->attributes();
                    if (!isset($attr->namespace)) {
                        $attr->namespace = \Environment::namespace();
                    }
                    $elements['entities'][str_replace('_','/',(string)$attr->namespace.'/'.(string)$attr->class)] = true;
                    break;
                case 'mongo'        : 
                    $attr = $subelements->attributes();
                    if (!isset($attr->namespace)) {
                        $attr->namespace = \Environment::namespace();
                    }
                    $elements['collections'][str_replace('_','/',(string)$attr->namespace.'/'.(string)$attr->class)] = true;
                    break;
                case 'access'        : 
                    $attr = $subelements->attributes();
                    if (!isset($attr->namespace)) {
                        $attr->namespace = \Environment::namespace();
                    }
                    $elements['access'][str_replace('_','/',(string)$attr->namespace.'/'.(string)$attr->class)] = true;
                    break;                
                case 'parameter'    :
                    $options = $this->extractParameterOptions($attr = $subelements->attributes());
                    $elements['parameters'][(string)$attr->name] = $options;
                    break;
                default:
                    break;
            }
            if (count($subelements)) {
                $elements = $this->extractElements($subelements,$elements);
            }        
        }
        return $elements;
    }
    
    /**
     * Since everything is a service, we should register them for easy lookup
     * 
     * @param type $namespace
     */
    protected function updateServiceDirectory($namespace=null) {
        $this->output('DIRECTORY','Generating Service Directory');
        if ($namespace) {
            Humble::entity('humble/service/directory')->setNamespace($namespace)->delete();
            if ($module = Humble::module($namespace)) {
                $services = Humble::entity('humble/service/directory');
                if (is_dir($location = 'Code/'.$module['package'].'/'.$module['controller'])) {
                    $dh = dir($location);
                    while ($controller = $dh->read()) {
                        if (($controller == '.') || ($controller == '..') || is_dir($location.'/'.$controller)) {
                            continue;
                        }
                        foreach (simplexml_load_file($location.'/'.$controller) as $actions) {
                            foreach ($actions as $action) {
                                $attrs      = ['description'=>[],'parameters'=>[],'helpers'=>[],'models'=>[],'entities'=>[],'collections'=>[],'header'=>[],'access'=>[]];
                                $attr       = $action->attributes();
                                $attrs['header']['passalong']  = (isset($attr->passalong) ? (string)$attr->passalong : "");
                                $attrs['header']['blocking']   = (isset($attr->blocking)  ? (string)$attr->blocking  : "YES");
                                $attrs['header']['response']   = (isset($attr->response)  ? (string)$attr->response  : "NO");
                                $attrs['header']['output']     = (isset($attr->output)    ? (string)$attr->output : 'text/html');
                                $attrs['header']['mapped']     = (isset($attr->map)       ? (string)$attr->map : '');                                
                                $services->reset()->setNamespace($namespace)->setController(str_replace('.xml','',$controller))->setAction((string)$attr->name);
                                if (count($action)) {
                                    $attrs       = $this->extractElements($action,$attrs);
                                }
                                //file_put_contents('attrs.txt',print_r($attrs,true),FILE_APPEND);
                                foreach ($attrs as $type => $attr) {
                                    $method = 'set'.ucfirst($type);
                                    $services->$method($attr);
                                }
                                $services->save();
                            }
                        }
                    }
                }
            }
        }
        $this->output('DIRECTORY','Finished Generating Directory');
    }
    
    /**
     * Copies the modules images over to the directory from where they get served
     * 
     * @param type $prefix
     * @param type $structure
     */
    private function updateImages($prefix,$structure,$module) {
        $this->output('IMAGES','Processing Images');
        $images     = $structure->images->source;
        if (is_dir('Code/'.$module->package.'/'.str_replace('_','/',$images))) {
            $sourceDir      = 'Code/'.$module->package.'/'.str_replace('_','/',$images);
            $destination    = "../images/".$this->namespace;
            @mkdir($destination,0775,true);
            $skipIfThere = false;
            $this->copyDirectory($sourceDir, $destination,$skipIfThere);
        }
        $this->output('IMAGES','Finished Processing Images');
    }

    /**
     * If a file in the directory has a more recent date then the last update date, run that SQL
     *
     * @param type $package
     * @param type $namespace
     * @param type $source
     */
    private function updateSchema($package,$namespace,$source)    {
        $this->output('SCHEMA','Processing any schema updates');
        $dir = @dir($path = 'Code/'.$package.'/'.str_replace('_','/',$source));
        $module = Humble::module($namespace);
        if (!$module) {
            $module['last_updated'] = 0;
        }
        $helper = Humble::helper('humble/directory');
        if (!is_dir($path)) {
            @mkdir($path,0775,true);
        }
        $files  = $helper->filesInDirectory($path);
        $lastUpdate = strtotime($module['last_updated']);
        foreach ($files as $file) {
            $fileUpdate = filemtime($file);
            if ($fileUpdate > $lastUpdate) {
                $statements = explode(';',file_get_contents($file));
                foreach ($statements as $statement) {
                    if (trim($statement) != "") {
                        $this->_db->query($statement);
                    }
                }
            }
        }
        $this->output('SCHEMA','Finished Processing schema updates');
    }
    
    /**
     * Updates the modules directory structure in the database using the configuration file
     * 
     * @param type $namespace
     * @param type $structure
     * @param type $module_data
     */
    public function updateStructure($namespace,$structure,$module_data) {
        $module = Humble::entity('humble/modules')->setNamespace($namespace);
        $module->load();
        $module->setRequired(isset($module_data->required) ? $module_data->required : "N");
        $module->setTemplater($module_data->use);
        $module->setPackage($this->package = $module_data->package);
        $module->setModels($models     = $structure->models->source);
        //get the directory name of the root of this module... used for the documentor later
        $this->module = (strpos('/',$models) ? explode('/',$models) : explode('_',$models));
        $this->module = $this->module[0];
        $module->setHelpers($structure->helpers->source);
        $module->setController($structure->controllers->source);
        $module->setConfiguration($structure->configuration->source);
        $module->setControllerCache($structure->controllers->cache);
        $module->setViews($structure->views->source);
        $module->setViewsCache($structure->views->cache);
        $module->setEntities($structure->entities->source);
        $module->setRpcMapping(isset($structure->RPC) ? $structure->RPC->source : "");
        $module->setImages($structure->images->source);
        if (is_dir('Code/'.$module_data->package.'/'.str_replace('_','/',$structure->images))) {
            $sourceDir      = 'Code/'.$module_data->package.'/'.str_replace('_','/',$structure->images);
            $destination    = "../images/".$namespace;
            @mkdir($destination,0775,true);
            $this->copyDirectory($sourceDir, $destination);
        }
        $module->setImagesCache($structure->images->cache);
        $module->setSchemaInstall(isset($structure->schema->install) ? $structure->schema->install : '');
        $module->setSchemaUpdate(isset($structure->schema->update) ? $structure->schema->update : '');
        $module->setSchemaLayout(isset($structure->schema->layout) ? $structure->schema->layout : '');
        $module->setResourcesJs(isset($structure->resources->js) ? $structure->resources->js : '');
        $module->setResourcesSql(isset($structure->resources->sql) ? $structure->resources->sql : '');
        $module->setResourcesTemplates(isset($structure->resources->templates) ? $structure->resources->templates : '');
        $module->setEnabled((isset($module_data->required) && ($module_data->required == "Y")) ? "Y" : "N");
        $module->setEnabled("Y");  //override to always enabled
        $module->setTitle(addslashes($this->title));
        $module->setDescription(addslashes($this->description));
        $module->save();
    }
    
    /**
     * Will deliberately cache/recache the applications meta-data files
     * 
     * @param array $project (optional)
     */
    protected function cacheMetaData($project=false) {
        $project = ($project) ? $project : \Environment::getProject();
        Humble::cache('public_routes',json_decode(file_get_contents('Code/'.$project->package.'/'.$project->module.'/etc/public_routes.json')));
        Humble::cache('application',\Environment::getApplication());
        Humble::cache('api_policy',json_decode(file_get_contents('Code/'.$project->package.'/'.$project->module.'/etc/api_policy.json')));
        Humble::cache('project',json_decode(file_get_contents('../Humble.project')));
    }
    
    /**
     * Executes a modules update strategy
     * 
     * @param type $source
     * @return \SimpleXMLElement
     */
    public function update($source=false)  {
        $source = ($source!==false) ? $source : $this->getSource();
        $helper = Humble::helper('humble/data');
        $xml = '';
        if (file_exists($source)) {
          //  \Log::console('Starting Update Of: '.$source);
            if ($helper->isValidXML($xml = file_get_contents($source))) {
                $xml    = new SimpleXMLElement($xml);
                foreach ($xml as $namespace => $contents) {
                    $this->namespace    = $namespace;
                    $this->title        = $contents->title;
                    $this->version      = $contents->version;
                    $this->description  = $contents->description;
                    $this->authorName   = $contents->author->name;
                    $this->authorEmail  = $contents->author->email;
                    $this->weight       = (isset($contents->module->weight)) ? $contents->module->weight : 99;
                    $package            = $contents->module->package;
                    $module_name        = $contents->module->name;
                    $base               = 'Code/'.$package.'/';
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
                        $this->updateSchema($package,$namespace,$contents->structure->schema->update);
                    }
                    if (isset($contents->orm)){
                        $this->mongodb      = (isset($contents->orm->mongodb)) ? $contents->orm->mongodb : '';
                        $this->prefix       = $contents->orm->prefix;
                        $this->storeEntities($contents->orm,$this->prefix);
                    }
                    if (isset($contents->structure)) {
                        $this->updateStructure($namespace,$contents->structure,$contents->module);
                        if (isset($contents->module->workflow) && ($contents->module->workflow=='Y')) {
                            $this->deRegisterWorkflowComponents();
                            $this->registerWorkflowComponents();
                        }
                    }
                    if (isset($contents->structure->schema) && (isset($contents->structure->schema->layout))) {
                       // $this->generateLayoutSchema($this->package,$this->namespace,$contents->structure->schema->layout,$contents->orm->entities);
                    }
                    if (isset($contents->structure->images)) {
                        $this->updateImages($this->prefix,$contents->structure,$contents->module);
                    }
                    if (isset($contents->events)) {
                        $this->registerEvents($contents->events);
                    }
                    if (isset($contents->web)) {
                        $this->registerWebComponents($contents->web);
                    }
                    if (isset($contents->structure->frontend)) {
                       // $this->moveFrontEnd($contents->structure->frontend->source);
                    }
                    $ent = Humble::entity('humble/modules');
                    $ent->setNamespace($this->namespace);
                    $now = date('Y-m-d H:i:s');
                    $dat = $ent->load(true);
                    $ent->setLastUpdated($now);
                    if (!$dat['installed']) {
                        $ent->setInstalled($now);
                    }
                    $ent->save();
                    $this->compileControllers();
                    $update_file  = "Code\\".(string)$contents->module->package."\\".str_replace(["_","/"],["\\","\\"],(string)$contents->structure->models->source)."\\OnUpdate.php";
                    $update_class = "Code\\".(string)$contents->module->package."\\".str_replace(["_","/"],["\\","\\"],(string)$contents->structure->models->source)."\\OnUpdate";
                    if (file_exists($update_file) && class_exists($update_class)) {
                        $i = Humble::model($namespace.'/OnUpdate',true)->execute();
                    }
                    $data = $ent->load();
                    Humble::cache('module-'.$namespace,$data);
                    $this->output("SUMMARY","============================================================================");
                    $this->output("SUMMARY","= MODULE STATE [".$namespace."]");
                    $this->output("SUMMARY","============================================================================");
                    foreach ($data as $attribute => $value) {
                        $this->output("SUMMARY","[".$attribute."]: ".$value);
                    }
                    $this->output("SUMMARY","============================================================================");
                    //must log updated date
                    $this->updateServiceDirectory($namespace);  
                }
            } else {
                foreach ($helper->getErrors() as $error) {
                    $this->output('ERRORS',$error);
                }
              //  \Log::console($helper->getErrors());
            }
        } else {
            $this->output('','');
         //   \Log::console('Could not find source file for refresh: '.$source);
        }
        $this->cacheMetaData();
        return $xml;
    }
}
?>