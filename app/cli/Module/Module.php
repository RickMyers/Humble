<?php
require_once 'cli/CLI.php';
class Module extends CLI 
{

    public static     function install() {
        $args   = self::arguments();
        $ns     = $args['namespace'];
        $etc    = $args['etc'];
        if ($ns) {
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

    public static function uninstall() {
        $args   = self::arguments();
        $ns     = $args['namespace'];        
        if ($ns) {
            $module = \Humble::getModule($ns,true);
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
            $etc     = 'Code/'.$data['package'].'/'.str_replace("_","/",$data['configuration']).'/config.xml';
            $updater->output('BEGIN','Update Configuration File: '.$etc);
            $updater->update($etc);
        } else {
            print('===> ERROR: Unable to determine where configuration file is for: '.$namespace.'.  You should review the configuration file manually <==='."\n");
        }
    }
    
    /**
     * Updates a module, running any new pieces of SQL, registering schema changes, generating workflows, documenting, moving images, etc...
     */
    public static function updateModule($args=[]) {
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
     * Builds a new module, including its file structure
     * 
     * @return string
     */
    public static function build() {
        $args = self::arguments();
        $ns = $args['namespace'];
        $pk = $args['package'];
        $px = $ns.'_';
        $au = $args['author'];
        $md = $args['module'];
        $em = $args['email'];
        if ($ns && $pk && $px && $md) {
            $base = 'Code/'.$pk;
            $root = $base."/".$md;
            if (!is_dir($base)) {
               @mkdir($base,0775,true);
            }
            if (!is_dir($root)) {
                @mkdir($root);
                @mkdir($root.'/etc');
                @mkdir($root.'/Controllers');
                @mkdir($root.'/Controllers/Cache');
                @mkdir($root.'/Mobile');
                @mkdir($root.'/Mobile/Controllers');
                @mkdir($root.'/Mobile/Controllers/Cache');
                @mkdir($root.'/Mobile/Views');
                @mkdir($root.'/Mobile/Views/Cache');
                @mkdir($root.'/Views');
                @mkdir($root.'/Views/actions');
                @mkdir($root.'/Views/actions/Smarty3');
                @mkdir($root.'/Views/Cache');
                @mkdir($root.'/Mobile');
                @mkdir($root.'/Models');
                @mkdir($root.'/Helpers');
                @mkdir($root.'/Schema');
                @mkdir($root.'/Schema/Install');
                @mkdir($root.'/Schema/Update');
                @mkdir($root.'/Schema/DSL');
                @mkdir($root.'/Entities');
                @mkdir($root.'/RPC');
                @mkdir($root.'/web');
                @mkdir($root.'/web/js');
                @mkdir($root.'/web/app');
                @mkdir($root.'/web/css');
                @mkdir($root.'/web/edits');
                @mkdir($root.'/Images');
                $project     = Environment::getProject();
                $module      = Humble::getModule($project->namespace);
                $is_base     = (string)$project->namespace == $ns;
                $package     = $is_base ? 'Base'   : (string)$project->package;
                $module      = $is_base ? 'Humble' : (string)$project->module;
                $required    = $is_base ? 'Y'      : 'N';
                $root        = is_dir('Code/'.$project->package.'/'.$project->module.'/lib/sample/module') ? 'Code/'.$project->package.'/'.$project->module : "Code/Base/Humble";
                $srch        = ["&&namespace&&","&&prefix&&","&&author&&","&&module&&","&&package&&",'&&email&&','&&FACTORY&&','&&base_package&&','&&base_module&&','&&required&&'];
                $repl        = [$ns,$px,$au,$md,$pk,$em,$project->factory_name,$package,$module,$required];
                $templates   = [$root."/lib/sample/module/Controllers/actions.xml"];
                $out         = ["Code/".$pk."/".$md."/Controllers/actions.xml"];
                $templates[] = $root."/lib/sample/module/etc/config.xml";                  $out[] = "Code/".$pk."/".$md."/etc/config.xml";
                $templates[] = $root."/lib/sample/module/RPC/mapping.yaml";                $out[] = "Code/".$pk."/".$md."/RPC/mapping.yaml";
                $templates[] = $root."/lib/sample/module/Views/actions/Smarty3/open.tpl";  $out[] = "Code/".$pk."/".$md."/Views/actions/Smarty3/open.tpl";
                $templates[] = $root."/lib/sample/module/web/js/actions.js";               $out[] = "Code/".$pk."/".$md."/web/js/".ucfirst($md).".js";
                $templates[] = $root."/lib/sample/module/web/css/template.css";            $out[] = "Code/".$pk."/".$md."/web/css/".ucfirst($md).".css";
                $templates[] = $root."/lib/sample/module/Models/Model.php.txt";            $out[] = "Code/".$pk."/".$md."/Models/Model.php";
                $templates[] = $root."/lib/sample/module/Helpers/Helper.php.txt";          $out[] = "Code/".$pk."/".$md."/Helpers/Helper.php";
                $templates[] = $root."/lib/sample/module/Entities/Entity.php.txt";         $out[] = "Code/".$pk."/".$md."/Entities/Entity.php";
                $templates[] = $root."/lib/sample/module/web/edits/template.json";         $out[] = "Code/".$pk."/".$md."/web/edits/sample_edit.json";
                foreach ($templates as $idx => $template) {
                    file_put_contents($out[$idx],str_replace($srch,$repl,file_get_contents($template)));
                }
                print("\n\nIf no errors, then the module was likely built.  At this point, run 'Humble --i namespace=$ns' to install the module, or access it through the administration screens.\n\n");
                return "Module likely created";
            }
        }
    }

}


