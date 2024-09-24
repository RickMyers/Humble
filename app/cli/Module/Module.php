<?php
require_once 'cli/CLI.php';
class Module extends CLI 
{

    /**
     * 
     */
    public static function install() {
        $args   = self::arguments();
        $ns     = $args['namespace'] ?? false;
        $etc    = $args['etc'] ?? false;
        if ($ns && $etc) {
            if (file_exists($etc) && ($xml = file_get_contents($etc))) {
                libxml_use_internal_errors(true);
                $doc    = new \DOMDocument('1.0', 'utf-8');
                $doc->loadXML($xml);
                $errors = libxml_get_errors();
                if ($errors) {
                    $level = array('1'=>'Warning', '2'=>'Error', '3'=>'Severe');
                    print("\nThe installation failed.\n\n");
                    print("Errors have been encountered in the XML configuration file ({$etc})\n\n");
                    foreach ($errors as $error) {
                        print("\t".$level[$error->level].": Line {$error->line}, column {$error->column} - ".$error->message);
                    }
                } else {
                    print("Installing...\n");
                    $utility = \Environment::getInstaller();
                    $utility->setSource($etc);
                    $utility->install();
                }
            } else {
                print("Couldn't read the XML config file\n\n");
            }
        } else {
            print("\n\nRequired parameters are namespace and configuration file location\n\n");
        }
    }

    /**
     * 
     */
    public static function uninstall() {
        $args   = self::arguments();
        $ns     = $args['namespace'];        
        if ($ns) {
            $module = \Humble::module($ns,true);
            print_r($module);
            if ($module) {
                $utility = \Humble::model('humble/utility');
                $utility->setPackage($module['package']);
                $utility->setNamespace($module['namespace']);
                if ($utility->uninstall()) {
                    print("\n\nThe module was uninstalled, it is now safe to delete the module\n\n");
                } else {
                    print("\n\nAn error was encountered during uninstallation, the module was not uninstalled\n\n");
                };
            } else {
                print("\nThe module represented by namespace [".$ns."] has either already been uninstalled or does not exist\n\n");
            }
        }
    }

    /**
     * Enables a module by setting the enabled flag to 'Y'
     * 
     * @param array $args (optional)
     */    
    public static function enable($args=false) {
        $args = $args ? $args :self::arguments();
        $ns = $args['namespace'];
        print("Enabling ".$ns."\n\n");
        $mod = \Humble::entity("humble/modules")->setNamespace($ns)->setEnabled('Y')->save();
    }

    /**
     * Disables a module by setting the enabled flag to 'N'
     * 
     * @param array $args (optional)
     */
    public static function disable($args=false) {
        $args = $args ? $args :self::arguments();
        $ns = $args['namespace'];        
        print("Disabling ".$ns."\n\n");
        \Humble::entity("humble/modules")->setNamespace($ns)->setEnabled('N')->save();
    }
    
    /**
     * Runs the update process for a single module
     * 
     * @param type $updater
     * @param type $namespace
     */
    protected static function updateIndividualModule($updater,$namespace) {
        $data = Humble::entity('humble/modules')->setNamespace($namespace)->load(true);
        if (isset($data['configuration']) && $data['configuration']) {
            $etc     = 'Code/'.$data['package'].DIRECTORY_SEPARATOR.''.str_replace("_","/",$data['configuration']).DIRECTORY_SEPARATOR.'config.xml';
            $updater->output('BEGIN','Update Configuration File: '.$etc);
            $updater->update($etc);
        } else {
            print('===> ERROR: Unable to determine where configuration file is for: '.$namespace.'.  You should review the configuration file manually <==='."\n");
        }
    }
    
    /**
     * Updates a module, running any new pieces of SQL, registering schema changes, generating workflows, documenting, moving images, etc...
     */
    public static function update($args=[]) {
        $args      = count($args) ? $args : self::arguments();
        $namespace = $args['namespace'];
        $workflows = $args['workflow'] ?? false;
        if ($namespace) {
            $modules = ($namespace==='*') ? \Humble::entity('humble/modules')->setEnabled('Y')->fetch() : explode(',',$namespace);
            print("\n\nThe following modules will be updated:\n\n"); $ctr=0;
            foreach ($modules as $module) {
                print("\t".++$ctr.') '.(is_array($module) ? $module['namespace'] : $module)."\n");
            }
            print("\n");
            $updater = \Environment::getUpdater();    
            foreach ($modules as $module) {
                $namespace = (is_array($module) ? $module['namespace'] : $module);
                $updater->output('BEGIN','');
                $updater->output('BEGIN',"=== Beginning update of Namespace: ".$namespace." ===");
                $updater->registerEntities($namespace);
                self::updateIndividualModule($updater->reset(),$namespace);
                //if (strtoupper($workflows)==='Y') {
                    $updater->generateWorkflows($namespace);
                //}
                //print(ob_get_clean());
            }
        } else {
            print('I need the namespace of the module to update passed in [namespace=ns]');
        }
    }
    
    /**
     * 
     */
    public static function activate() {
        $args = self::arguments();
        self::build($args);
        self::install([
            $args['namespace'],
            'Code/'.$args['package'].DIRECTORY_SEPARATOR.''.$args['module'].DIRECTORY_SEPARATOR.'etc/config.xml'
        ]);
        self::enable($args);
    }
    
    protected static function copyFiles($root,$files,$search=[],$replace=[]) {
        foreach ($files as $file) {
            if (!file_exists($root.DIRECTORY_SEPARATOR.$file->source)) {
                print("File: ".$root.DIRECTORY_SEPARATOR.$file->source." Not Found\n");
            } 
            if (!file_put_contents($dest = str_replace($search,$replace,$file->destination),str_replace($search,$replace,file_get_contents($root.DIRECTORY_SEPARATOR.$file->source)))) {
                print('Failed to write: '.$dest."\n");
            }
        }           
    }
    /**
     * Builds a new module, including its file structure
     * 
     * @return string
     */
    public static function build() {
        $result  = 'Module not built';
        $project = \Environment::getProject();
        $args    = self::arguments();
        $ns      = $args['namespace'];
        $pk      = $args['package'];
        $px      = $ns.'_';
        $au      = $args['author'] ?? ($project->author ?? '');
        $md      = $args['module'];
        $em      = $args['email'] ?? ($project->author ?? '');
        $mn      = $args['main_module'] ?? false;
        $mod     = json_decode(file_get_contents('Code/Framework/Humble/lib/sample/install/module.json'));
        if ($mod && $ns && $pk && $px && $md) {
            $base = 'Code'.DIRECTORY_SEPARATOR.$pk;
            $src  = 'Code/Framework/Humble';
            $root = $base."/".$md;
            if (!is_dir($base)) {
               @mkdir($base,0775,true);
            }
            if (!is_dir($root)) {
                @mkdir($root,0775,true);
                foreach ($mod->structure->basic as $path) {
                    mkdir($root.DIRECTORY_SEPARATOR.$path,0775,true);
                }
                $package     = $mn ? 'Framework' : (string)$project->package;
                $module      = $mn ? 'Humble'    : (string)$project->module;
                $required    = $mn ? 'Y'           : 'N';
                $main_module = $mn ? ucfirst(strtolower($project->namespace))." = {}" : "";  //if this is the main module, of which there can be only one, we will need to add an extra bit of JS
                $search      = ["&&MAIN_MODULE&&","&&PROJECT&&","&&NAMESPACE&&","&&PREFIX&&","&&AUTHOR&&","&&MODULE&&","&&PACKAGE&&",'&&EMAIL&&','&&FACTORY&&','&&BASE_PACKAGE&&','&&BASE_MODULE&&','&&REQUIRED&&'];
                $replace     = [$main_module,ucfirst(strtolower($project->namespace)),$ns,$px,$au,$md,$pk,$em,$project->factory_name,$package,$module,$required];
                self::copyFiles($src,$mod->templates,$search,$replace);
                if ($mn) {
                    //This is the main module, so we have to copy some additional files
                    foreach ($mod->structure->main_module as $path) {
                        mkdir($root.DIRECTORY_SEPARATOR.$path,0775,true);
                    }                       
                    $parts       = explode('/',$project->landing_page);
                    $controller  = $parts[2];        $page        = $parts[3];         
                    $search[]    = '&&CONTROLLER&&'; $replace[]   = $controller;
                    $search[]    = '&&PAGE&&';       $replace[]   = $page;
                    $search[]    = '&&BASEDIR&&';    $replace[]   = '';
                    $search[]    = '&&PROJECT_NAME&&'; $project->project_name;
                    @mkdir("Code/".$pk."/".$md."/Views/".$controller."/Smarty/",0775,true);
                    @mkdir('cli/'.$md,0775,true);
                    self::copyFiles($src,$mod->main_module,$search,$replace);
                    self::copyFiles($src,$mod->copy,$search,$replace);
                } else {
                    self::copyFiles($src,$mod->regular_module,$search,$replace);
                }
                $result = "Module likely created, don't forget to install it";
                header('RC: 0');
            } else {
                $result = "Module already exists, if necessary delete the current one and try again";
                header('RC: 8');
            }
            
        } else {
            $result = 'Insufficient parameters passed to create the module';
            header('RC: 16');
        }
        return $result;
    }

    /**
     * Retrieves a pre-setup version of tailwind and installs it in a module
     */
    public static function tailwind() {
        $args       = self::arguments();
        if ($module = \Humble::module($args['namespace'])) {
            $install_path = 'Code/'.$module['package'].'/'.$module['module'].'/web/';
            @mkdir($install_path,0775,true);
            print("\nRetrieving Tailwindcss package from ".Environment::project('framework_url')."\n");
            file_put_contents('tailwind.zip',file_get_contents(Environment::project('framework_url').'/dist/tailwind.zip'));
            $zip = new ZipArchive;
            if ($zip->open('tailwind.zip') === TRUE) {
                $zip->extractTo($install_path);
                $zip->close();                
            } 
            @unlink('tailwind.zip');
            print("\nDone.\n");
        } else {
            print('That module is not enabled or does not exist'."\n");
        }
        
    } 

}



