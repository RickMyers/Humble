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
    
    /**
     * Builds a new module, including its file structure
     * 
     * @return string
     */
    public static function build() {
        $result  = 'Module not built';
        $project = \Environment::getProject();
        $args = self::arguments();
        $ns = $args['namespace'];
        $pk = $args['package'];
        $px = $ns.'_';
        $au = $args['author'] ?? ($project->author ?? '');
        $md = $args['module'];
        $em = $args['email'] ?? ($project->author ?? '');
        $mn = $args['main_module'] ?? false;
        if ($ns && $pk && $px && $md) {
            $base = 'Code'.DIRECTORY_SEPARATOR.$pk;
            $root = $base."/".$md;
            if (!is_dir($base)) {
               @mkdir($base,0775,true);
            }
            if (!is_dir($root)) {
                @mkdir($root);
                @mkdir($root.DIRECTORY_SEPARATOR.'etc');
                @mkdir($root.DIRECTORY_SEPARATOR.'Controllers');
                @mkdir($root.DIRECTORY_SEPARATOR.'Controllers/Cache');
                @mkdir($root.DIRECTORY_SEPARATOR.'Mobile');
                @mkdir($root.DIRECTORY_SEPARATOR.'Mobile/Controllers');
                @mkdir($root.DIRECTORY_SEPARATOR.'Mobile/Controllers/Cache');
                @mkdir($root.DIRECTORY_SEPARATOR.'Mobile/Views');
                @mkdir($root.DIRECTORY_SEPARATOR.'Mobile/Views/Cache');
                @mkdir($root.DIRECTORY_SEPARATOR.'Views');
                @mkdir($root.DIRECTORY_SEPARATOR.'Views/actions');
                @mkdir($root.DIRECTORY_SEPARATOR.'Views/actions/Smarty');
                @mkdir($root.DIRECTORY_SEPARATOR.'Views/Cache');
                @mkdir($root.DIRECTORY_SEPARATOR.'Resources');
                @mkdir($root.DIRECTORY_SEPARATOR.'Resources/js');
                @mkdir($root.DIRECTORY_SEPARATOR.'Resources/SQL');
                @mkdir($root.DIRECTORY_SEPARATOR.'Models');
                @mkdir($root.DIRECTORY_SEPARATOR.'Helpers');
                @mkdir($root.DIRECTORY_SEPARATOR.'Schema');
                @mkdir($root.DIRECTORY_SEPARATOR.'Schema/Install');
                @mkdir($root.DIRECTORY_SEPARATOR.'Schema/Update');
                @mkdir($root.DIRECTORY_SEPARATOR.'Schema/DSL');
                @mkdir($root.DIRECTORY_SEPARATOR.'Entities');
                @mkdir($root.DIRECTORY_SEPARATOR.'RPC');
                @mkdir($root.DIRECTORY_SEPARATOR.'web');
                @mkdir($root.DIRECTORY_SEPARATOR.'web/js');
                @mkdir($root.DIRECTORY_SEPARATOR.'web/app');
                @mkdir($root.DIRECTORY_SEPARATOR.'web/css');
                @mkdir($root.DIRECTORY_SEPARATOR.'web/edits');
                @mkdir($root.DIRECTORY_SEPARATOR.'Images');
                $project     = Environment::getProject();
                $is_base     = (string)$project->namespace == $ns;
                $package     = $is_base ? 'Framework'   : (string)$project->package;
                $module      = $is_base ? 'Humble'      : (string)$project->module;
                $required    = $is_base ? 'Y'           : 'N';
                $copy        = [];
                $dest        = [];
                $main_module = strtoupper($project->namespace)===strtoupper($ns) ? ucfirst(strtolower($project->namespace))." = {}" : "";  //if this is the main module, of which there can be only one, we will need to add an extra bit of JS
                $root        = is_dir('Code'.DIRECTORY_SEPARATOR.$project->package.DIRECTORY_SEPARATOR.''.$project->module.DIRECTORY_SEPARATOR.'lib/sample/module') ? 'Code'.DIRECTORY_SEPARATOR.$project->package.DIRECTORY_SEPARATOR.''.$project->module : "Code/Framework/Humble";
                $srch        = ["&&MAIN_MODULE&&","&&PROJECT&&","&&NAMESPACE&&","&&PREFIX&&","&&AUTHOR&&","&&MODULE&&","&&PACKAGE&&",'&&EMAIL&&','&&FACTORY&&','&&BASE_PACKAGE&&','&&BASE_MODULE&&','&&REQUIRED&&'];
                $repl        = [$main_module,ucfirst(strtolower($project->namespace)),$ns,$px,$au,$md,$pk,$em,$project->factory_name,$package,$module,$required];
                $templates   = [$root."/lib/sample/module/Controllers/actions.xml"];       $out   = ["Code/".$pk."/".$md."/Controllers/actions.xml"];
                $templates   = [$root."/lib/sample/module/Controllers/admin.xml"];         $out   = ["Code/".$pk."/".$md."/Controllers/admin.xml"];                
                $templates[] = $root."/lib/sample/module/etc/config.xml";                  $out[] = "Code/".$pk."/".$md."/etc/config.xml";
                $templates[] = $root."/lib/sample/module/RPC/mapping.yaml";                $out[] = "Code/".$pk."/".$md."/RPC/mapping.yaml";
                $templates[] = $root."/lib/sample/module/Views/actions/Smarty/open.tpl";   $out[] = "Code/".$pk."/".$md."/Views/actions/Smarty/open.tpl";
                $templates[] = $root."/lib/sample/module/Views/admin/Smarty/app.tpl";      $out[] = "Code/".$pk."/".$md."/Views/admin/Smarty/app.tpl";                
                $templates[] = $root."/lib/sample/module/web/css/template.css";            $out[] = "Code/".$pk."/".$md."/web/css/".ucfirst($md).".css";
                $templates[] = $root."/lib/sample/module/Models/Model.php.txt";            $out[] = "Code/".$pk."/".$md."/Models/Model.php";
                $templates[] = $root."/lib/sample/module/Helpers/Helper.php.txt";          $out[] = "Code/".$pk."/".$md."/Helpers/Helper.php";
                $templates[] = $root."/lib/sample/module/Entities/Entity.php.txt";         $out[] = "Code/".$pk."/".$md."/Entities/Entity.php";
                $templates[] = $root."/lib/sample/module/web/edits/template.json";         $out[] = "Code/".$pk."/".$md."/web/edits/sample_edit.json";
                $templates[] = $root."/lib/sample/module/AdminApps.xml";                   $out[] = "Code/".$pk."/".$md."/AdminApps.xml";
                $copy[]      = $root."/images/icons/admin_app_icon.png";                   $dest[]= "Code/".$pk."/".$md."/Images/admin_app_icon.png";               
                if ($mn) {
                    //This is the main module, so we have to copy some additional files
                    $parts       = explode('/',$project->landing_page);
                    $controller  = $parts[2];        $page        = $parts[3];         
                    $srch[]      = '&&CONTROLLER&&'; $repl[]      = $controller;
                    $srch[]      = '&&PAGE&&';       $repl[]      = $page;
                    mkdir("Code/".$pk."/".$md."/Views/".$controller."/Smarty/",0775,true);
                    $templates[] = $root."/lib/sample/install/Views/index.html";     $out[] = "Code/".$pk."/".$md."/Views/".$controller."/Smarty/index.tpl";
                    $templates[] = $root."/lib/sample/install/Views/page.html";      $out[] = "Code/".$pk."/".$md."/Views/".$controller."/Smarty/".$page.".tpl";
                    $templates[] = $root."/lib/sample/install/Views/404.html";       $out[] = "Code/".$pk."/".$md."/Views/".$controller."/Smarty/404.tpl";
                    $templates[] = $root."/lib/sample/module/web/js/mainactions.js";     $out[] = "Code/".$pk."/".$md."/web/js/".ucfirst($md).".js";
                    $templates[] = $root."/lib/sample/install/Controllers/base.xml"; $out[] = "Code/".$pk."/".$md."/Controllers/".$controller.".xml";
                    $templates[] = $root."/lib/sample/install/Entities/Users.php.txt";  $out[] = "Code/".$pk."/".$md."/Entities/Users.php";
                    $templates[] = $root."/lib/sample/install/Models/User.php.txt";  $out[] = "Code/".$pk."/".$md."/Models/User.php";
                    $templates[] = $root."/lib/sample/install/public_routes.json";   $out[] = "etc/public_routes.json";
                } else {
                    $templates[] = $root."/lib/sample/module/web/js/actions.js";               $out[] = "Code/".$pk."/".$md."/web/js/".ucfirst($md).".js";
                }
                    
 
                foreach ($templates as $idx => $template) {
                    if (!file_put_contents($out[$idx],str_replace($srch,$repl,file_get_contents($template)))) {
                        print('Problem: '.$out[$idx].' && '.$template."\n");
                    }
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
        if ($module = Humble::module($args['namespace'])) {
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
        }
        print("\nDone.\n");
    }

}



