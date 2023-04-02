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
        --cc, --CC, Create a Controller
        --cm, --CM, Create a Component (Helper,Model,Entiy)
        --activate      Build, Install, and Enable a module
        --use           Update a module using the relative location of a configuration file [etc=Code/Base/Humble/etc/config.xml]
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



    //--------------------------------------------------------------------------
    function checkPrefixAvailability($args) {
        $px = fetchParameter('prefix',processArgs($args));
        if ($px) {
            $check = \Humble::entity('humble/modules');
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
    //--------------------------------------------------------------------------
    function activateModule($args) {
        $ns = fetchParameter('namespace',processArgs($args));
        $pk = fetchParameter('package',processArgs($args));
        $md = fetchParameter('module',processArgs($args));        
        createModuleDirectories($args);
        installModule([
            $ns,
            'Code/'.$pk.'/'.$md.'/etc/config.xml'
        ]);
        enableModule([$ns]);
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
    //--------------------------------------------------------------------------

    //--------------------------------------------------------------------------
    function updateUsingConfigurationFile($args) {
        $updater = \Environment::getUpdater(); 
        if ($etc= fetchParameter('etc',processArgs($args))) {
            if (file_exists($etc)) {
                $updater->output('BEGIN','Update Configuration File: '.$etc);
                $updater->update($etc);                
            } else {
                print("\nConfig file does not exist\n\n");
            }
        } else {
            print("\nMust pass in the location of the configuration file\n\n");
        }

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
            $util       = Humble::helper('humble/directory');
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
            $util       = Humble::helper('humble/directory');
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
    function addUser($args) {
        $parms = processArgs($args);
        $uname = fetchParameter('user_name',$parms);
        $passw = fetchParameter('password',$parms);
        $first = fetchParameter('first_name',$parms);
        $last  = fetchParameter('last_name',$parms);
        $uid   = fetchParameter('uid',$parms);
        if ($uname && $passw) {
            Humble::entity('humble/users')->newUser($uname,MD5($passw),$first,$last,$uid);
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
        $ignore = array_merge(['Docs/*','/images/*','/app/allowed.json','/app/Constants.php','/app/vendor/*','**/cache/*','**/Cache/*','/app/Workflows'],array_keys($content['xref']));
        $ignore = array_merge(['app/cli/Component/*','app/cli/Directive/*','app/cli/Workflow/*','app/cli/Framework/*','app/cli/System/*','app/cli/Module/*'],$ignore);
        $zip->addFromString('.gitignore',implode("\n",$ignore));
        //$zip->addFromString('.manifest',implode("\n",$content['xref']));
        $zip->close();
        chdir('app');
    }
    //--------------------------------------------------------------------------

    //--------------------------------------------------------------------------
    function exportWorkflows($args) {
        if ($target = fetchParameter(['destination','dst','dest','ds'],processArgs($args))) {
            //now get if they want to include all "all = fetchParameter(['all'],processArgs($args))
            $exporter = Humble::model('paradigm/workflow'); $dest_id = '';
            if (!preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$target)) {
                if ($data = Humble::entity('paradigm/import/sources')->setName($target)->load(true)) {
                    $target = $data['source']; $dest_id = $data['id'];
                } else {
                    die('You must either pass the URL of the destination, or a valid alias of the destination');
                }
            } else {
                if ($data = Humble::entity('paradigm/import/sources')->setSource($target)->load(true)) {
                    $dest_id = $data['id'];
                } else {
                    die('Unable to export to that destination, please consult the import sources table');
                }
            }
            foreach (Humble::entity('paradigm/workflows')->setActive('Y')->fetch() as $workflow) {
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
        chdir('app');                                                           //being called from distribution script
    }
    if (isset($args) && count($args)) {                                         //If included instead of being called, assign the args array to the argv array to make it look like it was called
        $argv = [];
        foreach ($args as $arg => $value) {
            $argv[$arg] = $value;
        }
    }
    if (!isset($argv) || !count($argv)) {
        print(file_get_contents('Module.php'));                                 //looks a bit crazy, but this basically says, if I wasn't called with arguments, someone is likely trying to download me, and this resolves that intention
        die();
    }
    $args = array_slice($argv,1);
    prep($args);
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
                case 'use':
                    updateUsingConfigurationFile(array_slice($args,1));
                    break;
                case 'activate':
                    activateModule(array_slice($args,1));
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