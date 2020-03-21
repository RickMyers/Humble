<?php
namespace Code\Base\Humble\Helpers;
use SimpleXMLElement;
use Humble;
/*
 * Updates the environment.
 *
 * What this does:
 *  o Clears setup and reinstalls structure
 *  o Checks for any SQL Updates, runs those
 *  o Copies any images that aren't already deployed
 *
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

    /**
     *
     * @param type $prefix
     * @param type $structure
     */
    private function updateImages($prefix,$structure,$module)
    {
        $images     = $structure->images->source;
        if (is_dir('Code/'.$module->package.'/'.str_replace('_','/',$images))) {
            $sourceDir      = 'Code/'.$module->package.'/'.str_replace('_','/',$images);
            $destination    = "../images/".$this->namespace;
            @mkdir($destination,0775,true);
            $skipIfThere = false;
            $this->copyDirectory($sourceDir, $destination,$skipIfThere);
        }
    }

    /**
     * If a file in the directory has a more recent date then the last update date, run that SQL
     *
     * @param type $package
     * @param type $namespace
     * @param type $source
     */
    private function updateSchema($package,$namespace,$source)    {
        $dir = @dir($path = 'Code/'.$package.'/'.str_replace('_','/',$source));
        $module = Humble::getModule($namespace);
        if (!$module) {
            $module['last_updated'] = 0;
        }
        $helper = Humble::getHelper('humble/directory');
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
    }
    public function updateStructure($namespace,$structure,$module_data) {
        $module = Humble::getEntity('humble/modules')->setNamespace($namespace);
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
        $module->setEnabled((isset($module_data->required) && ($module_data->required == "Y")) ? "Y" : "N");
        $module->setEnabled("Y");  //override to always enabled
        $module->setTitle(addslashes($this->title));
        $module->setDescription(addslashes($this->description));
        $module->save();
    }
    /**
     *
     * @param type $source
     * @return \SimpleXMLElement
     */
    public function update($source=false)  {
        $source = ($source!==false) ? $source : $this->getSource();
        $helper = Humble::getHelper('humble/data');
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
                    $ent = Humble::getEntity('humble/modules');
                    $ent->setNamespace($this->namespace);
                    $now = date('Y-m-d H:i:s');
                    $dat = $ent->load();
                    $ent->setLastUpdated($now);
                    if (!$dat['installed']) {
                        $ent->setInstalled($now);
                    }
                    $ent->save();
                    $this->compileControllers();
                    $update_file  = "Code\\".(string)$contents->module->package."\\".str_replace(["_","/"],["\\","\\"],(string)$contents->structure->models->source)."\\OnUpdate.php";
                    $update_class = "Code\\".(string)$contents->module->package."\\".str_replace(["_","/"],["\\","\\"],(string)$contents->structure->models->source)."\\OnUpdate";
                    if (file_exists($update_file) && class_exists($update_class)) {
                        $i = Humble::getModel($namespace.'/OnUpdate',true)->execute();
                    }
                    $data = $ent->load();
                    Humble::cache('module-',$data);
                    print_r($data);
                    //must log updated date
                }
            } else {
                print_r($helper->getErrors());
              //  \Log::console($helper->getErrors());
            }
          //  \Log::console('Did the update for: '.$source);
        } else {
         //   \Log::console('Could not find source file for refresh: '.$source);
        }
        return $xml;
    }
}
?>