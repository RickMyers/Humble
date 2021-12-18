<?php
$help = <<<HELP
/* -----------------------------------------------------------------------------
 *  This script is used to create a new module with all the required directory
 *  structure and initial files.
 *
 *
 *  Ex: Module.php --option(s) namespace=ns package=pk prefix=pr author=au module=md
 *
 *  option:
 *      --?, --h, This Help
 *      --o, --O, Toggle the application online or offline
 *      --c, --C, Check for namespace availability
 *      --p, --P, Preserve directory
 *      --r, --R, Restore preserved directory
 *      --b, --B, Build Initial Module
 *      --i, --I, Install module
 *      --u, --U, Update module
 *      --e, --E, Enable a module
 *      --d, --D, Disable a module
 *      --k, --K, Uninstall (kill) a module
 *      --v, --V, Version
 *      --s, --S, Application status
 *      --w, --W, Examine PHP code for workflow components
 *      --g, --G, Generate JSON Edits
        --z, --Z, Generate Workflows
 *      --l, --L, Toggle Local authentication
 *      --y, --Y, Compile a controller
 *      --x, --X, Check if a module prefix is available
 *      --a, --A, Remove AUTOINCREMENT=# from SQL dumps
 *      --adduser       Backend workaround to create a user, parameters are *user_name", "password", and optional "uid"       
 *      --package       Creates a new downloadable archive file of the Humble project
 *      --increment     Increments the minor version number by one, rolling over if it goes past 9
 *      --initialize    Initializes the project
 *      --export        Exports workflows to a pre-defined server/environment
 *      --patch         Updates the Humble Base Framework files with any new files, respecting manifested files
 *      --sync          Updates the core files
 * -----------------------------------------------------------------------------
 */
HELP;
    require_once('Humble.php');
    //ob_start();
    //--------------------------------------------------------------------------
    //Copied from PHPPro.blog
    //--------------------------------------------------------------------------
    function underscoreToCamelCase( $string, $first_char_caps = false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }
    //--------------------------------------------------------------------------
    //option functions
    //--------------------------------------------------------------------------
    function fetchParameter($parm,$list) {
        $parms = [];
        $parms = is_array($parm) ? array_flip($parm) : [$parm=>true];
        $value=false;
        foreach ($list as $key => $val) {
            if (isset($parms[$key])) {
                $value = $val;
                break;
            }
        }
        return $value;
    }
    //--------------------------------------------------------------------------
    function processArgs($args) {
        $parms = array();
        foreach ($args as $arg) {
            if (strpos($arg,'=')===false) {
                die('Invalid argument passed: '.$arg);
            }
            $arg = explode('=',$arg);
            $parms[$arg[0]] = $arg[1];
        }
        return $parms;
    }
    //--------------------------------------------------------------------------
    function printModule($mod) {
        print("\n\n");
        foreach ($mod as $idx => $val) {
            print("#".$idx."\t = ".$val.";\n");
        }
    }

    //--------------------------------------------------------------------------
    function toggleApplicationStatus() {
        $xml  = simplexml_load_string(file_get_contents('../application.xml'));
        $enabled = (int)$xml->status->enabled;
        $xml->status->enabled = $enabled ? 0 : 1;
        file_put_contents('../application.xml',$xml->asXML());
        $message = ($enabled) ? 'System Status: OFFLINE' : 'System Status: ONLINE';
        print("\n\n".$message."\n\n");
    }

    //--------------------------------------------------------------------------
    function toggleLocalAuthentication() {
        $xml  = simplexml_load_string(file_get_contents('../application.xml'));
        $enabled = (int)$xml->status->SSO->enabled;
        $xml->status->SSO->enabled = $enabled ? 0 : 1;
        file_put_contents('../application.xml',$xml->asXML());
        $message = ($enabled) ? 'Authentication Engine: LOCAL' : 'Authentication Engine: SSO';
        print("\n\n".$message."\n\n");
    }

    //--------------------------------------------------------------------------
    function checkNamespaceAvailability($args) {
        $ns = fetchParameter('namespace',processArgs($args));
        if ($ns) {
            $check = \Humble::getEntity('humble/modules');
            $check->setNamespace($ns);
            $mod = $check->load();
            if ($mod) {
                print("That namespace is already in use\n\n");
                print("Information on that module follows:\n");
                printModule($mod);
            } else {
                print("\nThat namespace ($ns) is available\n\n");
            }

        } else {
            die('Namespace, in the form of "namespace=myns" was not passed');
        }
    }
    //--------------------------------------------------------------------------
    function checkPrefixAvailability($args) {
        $px = fetchParameter('prefix',processArgs($args));
        if ($px) {
            $check = \Humble::getEntity('humble/modules');
            $check->setPrefix($px);
            $mod = $check->fetch();
            if ($mod && count($mod)>0) {
                print("That prefix is already in use\n\n");
                print("Information on that module follows:\n");
                printModule($mod[0]);
            } else {
                print("\nThat ORM prefix ($px) is available\n\n");
            }
        } else {
            die('Prefix, in the form of "prefix=mypx" was not passed');
        }
    }
    //--------------------------------------------------------------------------
    function createModuleDirectories($args) {
        $ns = fetchParameter('namespace',processArgs($args));
        $pk = fetchParameter('package',processArgs($args));
        $px = fetchParameter('prefix',processArgs($args));
        $au = fetchParameter('author',processArgs($args));
        $md = fetchParameter('module',processArgs($args));
        $em = fetchParameter('email',processArgs($args));
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
                $root        = is_dir('Code/'.$project->package.'/'.$project->module.'/lib/sample/module') ? 'Code/'.$project->package.'/'.$project->module : "Code/Base/Humble";
                $srch        = ["&&namespace&&","&&prefix&&","&&author&&","&&module&&","&&package&&",'&&email&&','&&FACTORY&&'];
                $repl        = [$ns,$px,$au,$md,$pk,$em,$project->factory_name];
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
            }
        } else {
            $msg = <<<TXT
            To create a module, you must pass namespace, package, module, prefix, and optionally author email.
TXT;
            print($msg);
        }
    }
    //--------------------------------------------------------------------------
    function displayHelp($help) {
        print_r($help);
    }
    //--------------------------------------------------------------------------
    function displayStatus($xml) {
        if ($xml->status->enabled == 1) {
            print("\n\n".date('Y-m-d H:i:s').'   <Application is enabled>'."\n\n");
        } else {
            print("\n\n".date('Y-m-d H:i:s').'   <Application is disabled>'."\n\n");
        }
    }
    //--------------------------------------------------------------------------
    function displayVersion($xml) {
        print("\n\n".$xml->version->framework."\n\n");
    }
    //--------------------------------------------------------------------------
    function getApplicationXML() {
        $data = (file_exists('../application.xml')) ? file_get_contents('../application.xml') : die("Error, application file not found");
        $xml  = simplexml_load_string($data);
        return $xml;
    }
    //--------------------------------------------------------------------------
    function enableModule($args) {
        $ns = $args[0];
        print("Enabling ".$ns."\n\n");
        $mod = \Humble::getEntity("humble/modules");
        $mod->setNamespace($ns);
        $mod->setEnabled('Y');
        $mod->save();
    }
    //--------------------------------------------------------------------------
    function disableModule($args) {
        $ns = $args[0];
        print("Disabling ".$ns."\n\n");
        \Humble::getEntity("humble/modules")->setNamespace($ns)->setEnabled('N')->save();
    }
    //--------------------------------------------------------------------------
    function incrementVersion($next=1) {
        print("CHANGING VERSION");
        $data   = getApplicationXML();
        $v      = explode('.',(string)$data->version->framework);
        for ($i=count($v)-1; $i>=0; $i-=1) {                                    //This is one of those ridiculously evil things in computer science
            $v[$i] = (int)$v[$i]+$next;
            if ($next  = ($v[$i]===10)) {
                $v[$i] = 0;
            }
        }
        $data->version->framework = (string)implode('.',$v);
        print("\nSetting version to ".$data->version->framework."\n\n");
        file_put_contents('../application.xml',$data->asXML());
    }

    //--------------------------------------------------------------------------
    function installModule($id) {
        $ns     = (isset($id[0])) ? $id[0] : false;
        $etc    = (isset($id[1])) ? $id[1] : false;
        if ($ns) {
            if (file_exists($id[1]) && ($xml = file_get_contents($id[1]))) {
                libxml_use_internal_errors(true);
                $doc = new \DOMDocument('1.0', 'utf-8');
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
    //--------------------------------------------------------------------------
    function uninstallModule($id) {
        $ns     = (isset($id[0])) ? $id[0] : false;
        if ($ns) {
            $module = \Humble::getModule($ns,true);
            print_r($module);
            if ($module) {
                $utility = \Humble::getModel('humble/utility');
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
    //--------------------------------------------------------------------------
    function updateIndividualModule($updater,$namespace) {
        $data = Humble::getEntity('humble/modules')->setNamespace($namespace)->load(true);
        if (isset($data['configuration']) && $data['configuration']) {
            $etc     = 'Code/'.$data['package'].'/'.str_replace("_","/",$data['configuration']).'/config.xml';
            $updater->output('BEGIN','Update Configuration File: '.$etc);
            $updater->update($etc);
        } else {
            print('===> ERROR: Unable to determine where configuration file is for: '.$namespace.'.  You should review the configuration file manually <==='."\n");
        }
    }
    //--------------------------------------------------------------------------
    function workflows($args) {
        $updater = \Environment::getUpdater();                    
        if ($namespace = fetchParameter(['namespace','ns'],processArgs($args))) {
            $updater->generateWorkflows($namespace);
        }
    }
    //--------------------------------------------------------------------------
    function updateModule($args) {
        $namespace = fetchParameter(['namespace','ns'],processArgs($args));
        $workflows = fetchParameter(['w','workflow','workflows'],processArgs($args));
        if ($namespace) {
            $modules = ($namespace==='*') ? \Humble::getEntity('humble/modules')->setEnabled('Y')->fetch() : explode(',',$namespace);
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
                updateIndividualModule($updater->reset(),$namespace);
                //if (strtoupper($workflows)==='Y') {
                    $updater->generateWorkflows($namespace);
                //}
                //print(ob_get_clean());
            }
        } else {
            print('I need the namespace of the module to update passed in [namespace=ns]');
        }
    }
    //--------------------------------------------------------------------------
    function processDocComment($md=false,$method=false) {
        $components = [];
        try {
            $comments = explode("\n",$md->getDocComment());
            foreach ($comments as $comment) {
                if (strpos($comment,'@workflow')!==false) {
                    $components[] = substr($comment,strpos($comment,'@')+1);
                }
            }
        } catch (ReflectionException $ex) {
         //  \Log::console($ex);
        }
        return $components;
    }
    //--------------------------------------------------------------------------
    function updateWorkflowComments($namespace,$model,$method,$md,$workflowComment) {
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
        $workflowComment->reset();
        $workflowComment->setNamespace($namespace);
        $workflowComment->setClass($model);
        $workflowComment->setMethod($method->name);
        $workflowComment->setComment(implode("\n",$comments));
    }
    //--------------------------------------------------------------------------
    function registerInlineEvent($namespace=false,$emit=false,$comment=false) {
        if ($namespace && $emit && $comment) {
            print('        Registering Inline Event '.$namespace.'/'.$emit.' ==> '.$comment."\n");            
            \Humble::getEntity('paradigm/events')->setNamespace($namespace)->setEvent($emit)->setComment($comment)->save();
        }
    }
    //--------------------------------------------------------------------------
    function scanForWorkflowComponents($file) {
        $namespace = false;
        $file    = str_replace(['\\',"/"],["_","_"],$file[0]);
        $parts   = explode('_',$file);
        $package = strtolower($parts[1]);
        $root    = strtolower($parts[2]."/".$parts[3]);
        print($root."\n");
        $data    = \Humble::getEntity('humble/modules')->setModels($root)->fetch();
        if ($data) {
            $data = $data->pop();
            $namespace = $data['namespace'];
        }
        if (!$namespace) {
            print(" Could not resolve the namespace for the class: ".$file."\n");
            die();
        } else {
            //########################################################################
            //# BE CAREFUL ABOUT HAVING ENTITY AND MODEL CLASSES WITH THE SAME NAME! #
            //# Of course, that's only if we allow entities to throw events, which   #
            //# we do not, so ignore this... nothing to see here... unless we start  #
            //# allowing entities to trigger events.                                 #
            //########################################################################
            $models = \Humble::getModels($namespace);
            //$models = array_merge($models,\Humble::getEntities($namespace));
           // $models = array_merge($models,\Humble::getHelpers($namespace)); //no, this is not a good idea, at least not yet
        }
        print('*** Scanning for workflow components within the Models of the Module identified by Namespace: '.$namespace."***\n");
        $workflowComponent  = \Humble::getEntity('paradigm/workflow_components');
        $workflowComment    = \Humble::getEntity('paradigm/workflow_comments');
        foreach ($models as $model) {
            print('Processing '.$model."...\n");
            $workflowComponent->reset();
            $workflowComponent->setNamespace($namespace);
            $workflowComponent->setComponent($model);
            $workflowComponent->delete();
            $class          = \Humble::getModel($namespace.'/'.$model);
            if (!method_exists($class, 'getClassName')) {
                print($model."\n");
                continue;
            }
            $name           = $class->getClassName();
            $reflection     = new ReflectionClass($name);
            $methods        = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $idx => $method) {
                $workflowComponent->reset();
                $workflowComponent->setNamespace($namespace);
                $workflowComponent->setComponent($model);
                $workflowComponent->setMethod($method->name);
                $m = new ReflectionMethod($class,$method->name);
                $c = $m->getDeclaringClass();
                if ($c->name !== $name) {
                    //this skips any methods belonging to the parent class
                    continue;
                }
                $comments      = processDocComment($reflection->getMethod($method->name),$method);
                $authorization = false;
                if ($comments) {
                    print("    Registering ".$method->name."\n");
                }
                foreach ($comments as $comment) {
                    $emit  = false; $inline_comment = false;                       //For inline event declaration                    
                    if (strtolower(substr(trim($comment),0,8)) === "workflow") {
                        $comment = substr(trim($comment),8);
                    }
                    $clauses   = explode(')',$comment);
                    foreach ($clauses as $clause) {
                        $value  = '';
                        $clause = trim($clause).')';
                        if (strpos($clause,'(') && (strpos($clause,')'))) {
                            $data  = explode('(',$clause);
                            $token = $data[0];
                            $data  = explode(')',$data[1]);
                            $value = $data[0];
                        } else {
                            $token = $clause;
                        }
                        switch (trim($token)) {
                            case "workflow"         :   //nop
                                                        break;
                            case "use"              :   $uses = explode(',',$value);
                                                        foreach ($uses as $use) {
                                                            $use = 'set'.ucfirst($use);
                                                            $workflowComponent->$use('Y');
                                                        }
                                                        break;
                            case "event"            :   $workflowComponent->setEventName($value);
                                                        break;
                            case "emit"             :   registerInlineEvent($namespace,($emit=$value),$inline_comment);
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
                            case "comment"          :   registerInlineEvent($namespace,$emit,($inline_comment=$value));
                                                        break;
                            case "cfg"              :
                            case "config"           :
                            case "configuration"    :   $workflowComponent->setConfiguration($value);
                                                        break;
                            default                 :   break;
                        }
                    }
                    $workflowComponent->save();
                    updateWorkflowComments($namespace,$model,$method,$reflection->getMethod($method->name),$workflowComment);
                }
            }
        }
        print('DONE!'."\n");
    }
    //--------------------------------------------------------------------------
    function generateJSONEdits($args) {
        $parms      = processArgs($args);
        $namespace  = fetchParameter('namespace',$parms);
        $entity     = fetchParameter('entity',$parms);
        if ($namespace && $entity) {
            $util   = \Environment::getInstaller();
            $module = \Humble::getModule($namespace);
            if ($module) {
                if (isset($module['schema_layout']) && $module['schema_layout']) {
                    $util->generateLayoutSchema($module['package'],$namespace,$module['schema_layout'],array(array($entity=>true)),true);
                } else {
                    print("No value set for the layout directory, please update the configuration XML.\n");
                }
            }
        }
    }
    //--------------------------------------------------------------------------
    function preserveDirectory($args) {
        $parms      = processArgs($args);
        $directory  = fetchParameter('directory',$parms);
        if (!$directory) {
            $directory  = fetchParameter('dir',$parms);
        }
        print('Attempting to copy: '.$directory."\n");
        if ($directory) {
            $util       = Humble::getHelper('humble/directory');
            $directory  = (substr($directory,0,1)==='/') ? substr($directory,1) : $directory;  //convert from absolute to relative path
            if (is_dir('../'.$directory)) {
                @mkdir('../../'.$directory,0775,true);
                $util->copyDirectory('../'.$directory,'../../'.$directory);
            } else {
                print("Directory doesn't exist\n");
            }
        } else {
            print('Directory not found');
        }
    }
    //--------------------------------------------------------------------------
    function restoreDirectory($args) {
        $parms      = processArgs($args);
        $directory  = fetchParameter('directory',$parms);
        if (!$directory) {
            $directory  = fetchParameter('dir',$parms);
        }
        print('Attempting to restore: '.$directory."\n");
        if ($directory) {
            $util       = Humble::getHelper('humble/directory');
            $directory  = (substr($directory,0,1)==='/') ? substr($directory,1) : $directory;  //convert from absolute to relative path
            if (is_dir('../../'.$directory)) {
                @mkdir('../'.$directory,0775,true);
                $util->copyDirectory('../../'.$directory,'../'.$directory);
            } else {
                print("Directory doesn't exist\n");
            }
        } else {
            print('Directory not found');
        }
    }
    //--------------------------------------------------------------------------
    function deStinkyTheSQL($args) {
        $parms = processArgs($args);
        $file  = fetchParameter('file',$parms);
        if ($file) {
            if (file_exists($file)) {
                $lines = [];
                foreach (explode("\n",file_get_contents($file)) as $row) {
                    if (($pos = strpos($row,"AUTO_INCREMENT="))!==false) {
                        $pre = substr($row,0,$pos);
                        $post = strpos(substr($row,$pos),' ');
                        $row = $pre.substr(substr($row,$pos),$post);
                    }
                    $lines[] = $row;
                }
                file_put_contents($file,implode("\n",$lines));
            } else {
                print('File specified ['.$file.'] does not exist');
            }
        } else {
            print('Must pass in an argument of file=###');
        }

    }
    //--------------------------------------------------------------------------
    function compileController($args) {
        $file       = fetchParameter('file',processArgs($args));
        print($file."\n");
        $compiler   = \Environment::getCompiler();
        $compiler->compileFile($file);
    }
    //--------------------------------------------------------------------------
    function scrub($str) {
        $srch = ["\n","\r","\t"];
        $repl = ["","",""];
        return str_replace($srch,$repl,$str);
    }
    //--------------------------------------------------------------------------
    function recurseDirectory($path) {
        $entries = [];
        if ($path) {
            if (!is_dir($path)) {
                print("What is up with this: ".$path."\n");
            }
            $dir = dir($path);
            while (($entry = $dir->read()) !== false ) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                if (is_dir($path.'/'.$entry)) {
                    $entries = array_merge($entries,recurseDirectory($path.'/'.$entry));
                } else {
                    $entries[] = $path.'/'.$entry;
                }
            }
        }
        return $entries;
    }
    //--------------------------------------------------------------------------
    function addUser($args) {
        $parms = processArgs($args);
        $uname = fetchParameter('user_name',$parms);
        $passw = fetchParameter('password',$parms);
        $first = fetchParameter('first_name',$parms);
        $last  = fetchParameter('last_name',$parms);
        $uid   = fetchParameter('uid',$parms);
        if ($uname && $passw) {
            Humble::getEntity('humble/users')->newUser($uname,MD5($passw),$first,$last,$uid);
        } else {
            print("Not enough data was passed to create a user.  user_name and password are minimum required fields.\n");
        }
    }
    //--------------------------------------------------------------------------
    function getManifestContent() {
        $content = [
            'manifest' => [],
            'files' => [],
            'exclude' => [],
            'xref' => []
        ];
        if (file_exists('Humble.manifest')) {
            $content['manifest'] = explode("\n",file_get_contents('Humble.manifest'));
        } else {
            die('Manifest file not found');
        }
        chdir('..');
        foreach ($content['manifest'] as $file) {
            if (substr($file,0,1) == '#') {
                continue;
            }
            if (substr($file,0,1) == '^') {
                $content['exclude'][trim(substr($file,1))] = $file;
                continue;
            }
            $file            = trim($file);
            $parts           = explode(' ',$file);
            $content['xref'][$parts[0]] = (isset($parts[1]) ? $parts[1] : $parts[0]);
            if (substr($file,strlen($file)-1,1)=='*') {
                $content['files'] = array_merge($content['files'],recurseDirectory(substr($file,0,strlen($file)-2)));
            } else {
                $content['files'][] = $file;
            }
        }
        chdir('app');
        return $content;
    }
    //--------------------------------------------------------------------------
    function syncProject($args) {
        $target = fetchParameter('target',processArgs($args));
        $sync   = [
            'identical' => [],   /* same in source and target */
            'missing'   => [],   /* missing in target */
            'special'   => [],   /* where the src and dest aren't the same */
            'changed'   => []    /* different in target than in source */
        ];

        if ($target && is_dir('../'.$target)) {
            $content = getManifestContent();
            chdir('..');
            foreach ($content['files'] as $file) {
                $exclude = false;
                foreach ($content['exclude'] as $mask => $type) {
                    if (strpos($file,$mask) !== false) {
                        $exclude = true;
                    }
                }
                if ($exclude || ($cnt = count(explode(' ',$file))>1)) {
                    if ($cnt > 1) {
                        $sync['special'][] = $file;
                    }
                    continue;
                }
                if (file_exists($target.'/'.$file)) {
                    $a = file_get_contents($file);
                    $b = file_get_contents($target.'/'.$file);
                    if ($a === $b) {
                        $sync['identical'][] = $file;
                    } else {
                        $sync['changed'][] = $file;
                    }
                } else {
                    $sync['missing'][] = $file;
                }
            }

            print("-------------------------------------------------------------\n");
            print("- THE FOLLOWING FILES WILL BE COPIED TO THE TARGET PROJECT  -\n");
            print("-------------------------------------------------------------\n");
            foreach ($sync['missing'] as $idx => $file) {
                print($idx." )\t".$file."\n");
            }
            print("\n-----------------------------------------------------------------\n");
            print("- THE FOLLOWING FILES WILL REPLACE FILES IN THE TARGET PROJECT  -\n");
            print("-----------------------------------------------------------------\n");
            foreach ($sync['changed'] as $idx => $file) {
                print($idx." )\t".$file."\n");
            }
            print("Do you wish to proceed? [Y/N]: ");
            $answer = scrub(fgets(STDIN));
            if (strtolower($answer) === 'y') {
                foreach ($sync['missing'] as $file) {
                    if (($lastPos = strrpos($file,'/'))!== false) {
                        if ($dir = substr($file,0,$lastPos)) {
                            @mkdir($target.'/'.$dir,0775,true);
                        }
                        copy($file,$target.'/'.$file);
                    }
                }
                foreach ($sync['changed'] as $file) {
                    copy($file,$target.'/'.$file);
                }
            } else {
                print("\n\nSync aborted");
            }
            chdir('app');
        } else {
            print("\nYou must specify the target project... [target=c:\\projects\\myproject\\ \n\n");
        }
    }
    //--------------------------------------------------------------------------
    function packageProject() {
        $content = getManifestContent();
        chdir('..');
        foreach ($content['files'] as $file) {
            if (!isset($content['xref'][$file])) {
                $content['xref'][$file] = $file;
            }
        }
        @mkdir('../packages/',0775);
        $xml        = simplexml_load_file('application.xml');
        $archive    = '../packages/Humble-Distro-'.(string)$xml->version->framework.'.zip';
        print("Creating archive ".$archive."\n");
        if (file_exists($archive)) {
            unlink($archive);
        }
        $zip = new ZipArchive();
        if ($zip->open($archive, ZipArchive::CREATE) !== true) {
            die('Wasnt able to create zip');
        };
        foreach ($content['xref'] as $src => $dest) {
            $exclude = false;
            foreach ($content['exclude'] as $mask => $type) {
                if (strpos($src,$mask) !== false) {
                    $exclude = true;
                }
            }
            if ($exclude) {
                continue;
            }
            if (file_exists($src) && is_file($src)) {
                $zip->addFile($src, $dest);
            }
        }
        //Now add manifest file in the form of a git ignore...
        $ignore = array_merge(['/images/*','/app/allowed.json','/app/Constants.php','/app/vendor/*','**/cache/*','**/Cache/*','/app/Workflows'],array_keys($content['xref']));
        $zip->addFromString('.gitignore',implode("\n",$ignore));
        //$zip->addFromString('.manifest',implode("\n",$content['xref']));
        $zip->close();
        chdir('app');
    }
    //--------------------------------------------------------------------------
    function performCoreUpdate($distro,$changed,$insertions,$matched,$ignored,$merged,$app,$version) {
        ob_start();
        print("\nPATCH REPORT\n########################################################\n\nMatched Files: ".$matched."\n\nThe following files will be updated by this process:\n\n");
        print("\nThe following files are on the local manifest indicating they should be IGNORED in the patch:\n\n");
        foreach ($ignored as $idx => $file) {
            print(str_pad($idx+1,5,"0",STR_PAD_LEFT).") ".$file."\n");
        }
        print("\nThe following files are on the local manifest indicating they should be MERGED in the patch:\n\n");
        foreach ($merged as $idx => $file) {
            print(str_pad($idx+1,5,"0",STR_PAD_LEFT).") ".$file."\n");
        }
        print("\nThe following files will be patched:\n\n");
        foreach ($changed as $idx => $file) {
            print(str_pad($idx+1,5,"0",STR_PAD_LEFT).") ".$file."\n");
        }
        print("\nThe following files are new and will be inserted by this patch:\n\n");
        foreach ($insertions as $idx => $file) {
            print(str_pad($idx+1,5,"0",STR_PAD_LEFT).") ".$file."\n");
        }
        print($report = ob_get_clean());
        file_put_contents('patch_report.txt',$report);
        print("\n\nIf you do not want some files updated, add those files to the Humble.local.manifest file and re-run this process.\n\nA copy of the patch review report shown above can be found in file 'patch_report.txt'.\n\n");
        print("Do you wish to continue [yes/no]? ");
        if (strtolower(scrub(fgets(STDIN))) === 'yes') {
            $app->version->framework = $version;
            file_put_contents('application.xml',$app->asXML());
            foreach ($changed as $file) {
                file_put_contents($file,$distro->getFromName($file));
            }
            foreach ($insertions as $file) {
                if (count($parts = explode('/',$file))>1) {
                    @mkdir(implode('/',array_slice($parts,0,count($parts)-1)),0775,true);
                }
                file_put_contents($file,$distro->getFromName($file));
            }
            chdir('app');
            print("Now running update...\n\n");
            updateModule(['ns=*']);
            chdir('..');
        } else {
            print("\n\nFramework update aborted.\n\n");
        }
    }
    //--------------------------------------------------------------------------
    function evaluateCoreDifferences($app,$project,$version) {
        $local_manifest = (file_exists('app/Humble.local.manifest')) ? json_decode(file_get_contents('app/Humble.local.manifest'),true) : ['merge'=>[],'ignore'=>[]];   //Load the manifest that tells us what files to not update
        if (file_exists('app/Humble.local.manifest')) {
            print("\n\n".'Found Local Manifest file...'."\n\n");
        }
        if (!$local_manifest) {
            die("\n\nERROR: Could not read Humble.local.manifest.  Check to see it exists or if there is a parsing issue with the file\n\n");
        }

        file_put_contents('distro_'.$version.'/humble.zip',file_get_contents($project['framework_url'].'/distro/fetch'));                                               //Download the current source base
        $changed    = []; $insertions = []; $source = []; $contents = []; $ignore = []; $merge = []; $matched = 0;
        $distro     = new ZipArchive();
        if ($distro->open('distro_'.$version.'/humble.zip')) {
            for ($i=0; $i< $distro->numFiles; $i++) {
                $contents[] = $distro->getNameIndex($i);
            }
        } else {
            die("\nFailed To open distro zip file\n");
        }
        foreach ($contents as $file_idx => $file) {
            print("processing ".$file."\n");
            if (file_exists($file)) {
                if (isset($local_manifest['ignore'][$file]) && $local_manifest['ignore'][$file]) {
                    $ignore[] = $file;
                } else if (isset($local_manifest['merge'][$file]) && $local_manifest['merge'][$file]) {
                    $merge[]  = $file;
                } else if ($distro->getFromIndex($file_idx) != file_get_contents($file)) {
                    $changed[] = $file;
                } else {
                    $matched++;
                }
            } else {
                $insertions[] = $file;
            }
        }
        performCoreUpdate($distro,$changed,$insertions,$matched,$ignore,$merge,$app,$version);
    }
    //--------------------------------------------------------------------------
    function patchFrameworkCore() {
        if (file_exists('../Humble.project')) {
            $project = json_decode(file_get_contents('../Humble.project'),true);
        } else {
            die("\nHumble project file not found.\n");
        }
        if (file_exists('../application.xml')) {
            $app = simplexml_load_file('../application.xml');
        } else {
            die("\Application XML file not found\n");
        }
        $canonical = json_decode(file_get_contents($project['framework_url']."/distro/version"),true);
        $canon_version = (int)str_replace(".","",(string)$canonical['version']);
        $local_version = (int)str_replace(".","",(string)$app->version->framework);
        $helper = Humble::getHelper('humble/directory');
        print("\n\nRunning patching report on core framework to version ".$canonical['version'].", please wait...\n\n");
        $distro = 'distro_'.$canonical['version'];
        chdir('..');
        @mkdir($distro,0775,true);
        evaluateCoreDifferences($app,$project,$canonical['version']);
        $helper->purgeDirectory($distro,true);
        @rmdir($distro);
        chdir('app');
    }
    //--------------------------------------------------------------------------
    function exportWorkflows($args) {
        if ($target = fetchParameter(['destination','dst','dest','ds'],processArgs($args))) {
            //now get if they want to include all "all = fetchParameter(['all'],processArgs($args))
            $exporter = Humble::getModel('paradigm/workflow'); $dest_id = '';
            if (!preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$target)) {
                if ($data = Humble::getEntity('paradigm/import/sources')->setName($target)->load(true)) {
                    $target = $data['source']; $dest_id = $data['id'];
                } else {
                    die('You must either pass the URL of the destination, or a valid alias of the destination');
                }
            } else {
                if ($data = Humble::getEntity('paradigm/import/sources')->setSource($target)->load(true)) {
                    $dest_id = $data['id'];
                } else {
                    die('Unable to export to that destination, please consult the import sources table');
                }
            }
            foreach (Humble::getEntity('paradigm/workflows')->setActive('Y')->fetch() as $workflow) {
                $exporter->setId($workflow['id'])->setDestinationId($dest_id)->export();
            }
        } else {
            die('At a minimum, you must pass in the destination, and optionally whether you want to include all instead of just active workflows');
        }
    }
    //--------------------------------------------------------------------------
    //begin main
    //--------------------------------------------------------------------------
    print("\nWorking on it...\n\n");
    //ob_start();
    if (substr(getcwd(),-3,3)!=='app') {
        chdir('app');           //being called from distribution script
    }
    if (!isset($argv) || !count($argv)) {
        print(file_get_contents('Module.php'));                                 //looks a bit crazy, but this basically says, if I wasn't called with arguments, someone is likely trying to download me, and this resolves that intention
        die();
    }
    $args = array_slice($argv,1);
    if ($args) {
        if (substr($args[0],0,2) == '--') {
            $cmd = substr($args[0],2);
            switch (strtolower($cmd)) {
                case 'c'    :
                    checkNamespaceAvailability(array_slice($args,1));
                    break;
                case 'p'    :
                    preserveDirectory(array_slice($args,1));
                    break;
                case 'o'    :
                    toggleApplicationStatus();
                    break;
                case 'x'    :
                    checkPrefixAvailability(array_slice($args,1));
                    break;
                case 'r'    :
                    restoreDirectory(array_slice($args,1));
                    break;
                case 'b'    :
                    createModuleDirectories(array_slice($args,1));
                    break;
                case 'i'    :
                    installModule(array_slice($args,1));
                    break;
                case 'u'    :
                    updateModule(array_slice($args,1));
                    break;
                case 'e'    :
                    enableModule(array_slice($args,1));
                    break;
                case 'd'    :
                    disableModule(array_slice($args,1));
                    break;
                case '?'    :
                case 'h'    :
                case 'help' :
                    displayHelp($help);
                    break;
                case 'k'    :   uninstallModule(array_slice($args,1));
                                break;
                case 's'    :
                    $xml = getApplicationXML();
                    displayStatus($xml);
                     break;
                case 'v'    :
                    $xml = getApplicationXML();
                    displayVersion($xml);
                    break;
                case 'w'    :
                    scanForWorkflowComponents(array_slice($args,1));
                    break;
                case 'z'    :
                    workflows(array_slice($args,1));
                    break;
                case 'g'    :
                    generateJSONEdits(array_slice($args,1));
                    break;
                case 'l'    :
                    toggleLocalAuthentication();
                    break;
                case 'a'    :
                    deStinkyTheSQL(array_slice($args,1));
                    break;
                case 'y'    :
                    compileController(array_slice($args,1));
                    break;
                case 'package'  :
                    packageProject();
                    break;
                case 'sync'     :
                    syncProject(array_slice($args,1));
                    break;
                case 'patch'  :
                    patchFrameworkCore();
                    break;
                case 'adduser' : 
                    addUser(array_slice($args,1));
                    break;
                case 'inc':
                case 'increment':
                case '+':
                    incrementVersion();
                    break;
                case 'export': 
                    exportWorkflows(array_slice($args,1));
                    break;
                default  :
                    die('Dont know how to process that command ('.$cmd.')');
                    break;
            }
        } else {
            $required = array('namespace'=>false, 'package'=>false, 'prefix'=>false, 'author'=>false);
            if ($args[0] == 'help') {
                print($help);
            } else {
                print("\n\tIncorrect flag format \n");
            }
        }
    } else {
        print('hows the weather out there...?');
    }

?>