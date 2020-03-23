<?php
$help = <<<HELP
/* -----------------------------------------------------------------------------
 *  This script is used to manage new projects or update them
 *
 *
 *  Ex: Humble.php option(s)
 *
 *  option:
 *      --help        This help
 *      --project     Creates the original project file
 *      --init        Same as --project
 *      --fetch       Initial install of repository
 *      --restore     Restores the Humble framework into an existing (Humble based) project
 * -----------------------------------------------------------------------------
 */
HELP;
function scrub($str) {
    $srch = ["\n","\r","\t"];
    $repl = ["","",""];
    return str_replace($srch,$repl,$str);
}
function humbleHeader() {
    $header = <<<HDR


    |_   _    |_      ._ _  |_  |  _
    |_) (/_   | | |_| | | | |_) | (/_


HDR;
    print($header);
}
/* ---------------------------------------------------------------------------------- */
function installedEextensionCheck() {

    exec('php -m',$modules);

    $required = [
    'bz2'=>false,
    'curl'=>false,
    'fileinfo'=>false,
    'gd'=>false,
    'json'=>false,
    'libxml'=>false,
    'mbstring'=>false,
    'memcache'=>false,
    'mongodb'=>false,
    'mysqli'=>false,
    'SimpleXML'=>false,
    'soap'=>false,
    'xml'=>false,
    'zip'=>false
    ];

    $ok = true;
    foreach ($required as $extension => $status) {
        $ok = $ok && ($required[$extension] = extension_loaded($extension));
    }
    if (!$ok) {
        print("The following extensions should/must be enabled within your php.ini file, please consult https://www.php.net to determine how to enable them\n\n\n");
        $ctr = 0;
        foreach ($required as $extension => $installed) {
            if (!$installed) {
                print(++$ctr.') '.$extension."\n");
            }
        }
        print("\nAfter enabling, please re-attempt the installation.\n");
        die();
    }    
}
/* ---------------------------------------------------------------------------------- */
function fetchProject($version,$framework_url,$update=false) {
    @mkdir('../extract',0775);  //going to test extraction to this location
    $distro = 'Humble-Distro.'.$version.'.zip';
    file_put_contents('../extract/'.$distro,file_get_contents($framework_url.'/distro/fetch'));
    $new_distro = new ZipArchive();
    if ($new_distro->open('../extract/'.$distro, ZipArchive::CREATE) !== true) {
        die('Wasnt able to open new distro');
    };
    $ctr = 0; $copy_ctr = 0; $skip_ctr = 0; $merge_ctr = 0; $ignore_ctr = 0;
    $local_manifest = false;
    if (file_exists('Humble.local.manifest')) {
        $local_manifest = json_decode(file_get_contents('Humble.local.manifest'),true);
    }
    if (!$local_manifest) {
        $local_manifest = ['merge'=>[],'ignore'=>[]];
    }
    while (($entry = $new_distro->getFromIndex($ctr))!==false) {
        $name = $new_distro->getNameIndex($ctr);
        if (isset($local_manifest['merge'][$name])) {
            $merge_ctr += 1;
            print('Merging '.$name."\n");
        } else if (!isset($local_manifest['ignore'][$name])) {
            $current = '';
            $dir = explode('/',$name);
            if (count($dir)>1) {
                $dir = implode('/',array_splice($dir,0,count($dir)-1));
                if (!is_dir($dir)) {
                    mkdir($dir,0775,true);
                }
            }
            if (file_exists('./'.$name)) {
                $current = file_get_contents('./'.$name);
            }
            if ($current != $entry) {
                if (file_put_contents('./'.$name,$entry)) {
                    $copy_ctr += 1;
                }
            } else {
                $skip_ctr += 1;
                print('Skipping '.$name."\n");
            }
        } else {
            $ignore_ctr += 1;
            print('Ignoring '.$name."\n");
        }
        $ctr++;
    }
    if (file_exists('../extract/'.$distro)) {
        $new_distro->close();
        unlink('../extract/'.$distro);
    }
    print("\n".'====================================='."\n");
    print("= Install/Update Report             =\n");
    print("=====================================\n");
    print("Total Files                      ".$ctr."\n");
    print("Files Skipped                    ".$skip_ctr."\n");
    print("Files Added/Replaced             ".$copy_ctr."\n");
    print("Files Merged                     ".$merge_ctr."\n");
    print("Files Ignored                    ".$ignore_ctr."\n");
    print("=====================================\n\n");

}
/* ---------------------------------------------------------------------------------- */
function initializeProject() {
    print("\n".'Do you wish to initialize a Humble project? [yes/no]: ');
    if (strtolower(scrub(fgets(STDIN))) === 'yes') {
        $create_project = true;
        if (file_exists('Humble.project')) {
            print('A Humble project exists already, do you wish to over write? [yes/no]: ');
           $create_project = (strtolower(scrub(fgets(STDIN))) === 'yes');
        }
        if ($create_project) {
            humbleHeader();
            $attributes     = ['project_name'=>'','project_url'=>'','factory_name'=>'','framework_url'=>'','module'=>'','namespace'=>'','package'=>'','landing_page'=>'', 'author'=>''];
            while (!$attributes['framework_url']) {
                print("\n\tHumble has its own framework updater but needs to know where to obtain the source from\n");
                print("\tPlease enter the URL for the Humble source [https://humble.enicity.com]: ");
                $attributes['framework_url']        = scrub(fgets(STDIN));
            }
            while (!$attributes['project_name']) {
                print("\n\tThe following question just wants to know an overall name for the project you intend to create with Humble\n");
                print("\tPlease enter the name for this project: ");
                $attributes['project_name']         = scrub(fgets(STDIN));
            }
            while (!$attributes['author']) {
                print("\n\tEmail for the author of this project (i.e. you@gmail.com):  ");
                $attributes['author']         = scrub(fgets(STDIN));
            }
            while (!$attributes['project_url']) {
                print("\n\tHumble has a two part installation, the first part downloads the framework, and the second part configures the framework for your project.\n");
                print("\tThe second part is configured using a web form, so a website (likely VHOST) has to already be created for your project.  You specify that website location below:\n");
                print("\tPlease enter the URL for this project: ");
                $attributes['project_url']          = scrub(fgets(STDIN));
            }
            while (!$attributes['factory_name']) {
                print("\n\tThis is where it gets personal.  A PHP Static Factory will be created for you which will extend the primary framework's Factory class. \n");
                print("\tYou will reference most of the Humble framework components through this 'rebranded' Factory.  It's also a great place to keep your own Factory methods.\n");
                print("\tPlease enter the name for the rebranded main Factory class: ");
                $attributes['factory_name']         = scrub(fgets(STDIN));
            }
            while (!$attributes['package']) {
                print("\n\tA package is nothing more than a directory.  During the creation of your application you can create as many 'packages' as you would like, here you are just creating your first.\n");
                print("\tPlease enter the package name that will contain the project module: ");
                $attributes['package']              = ucfirst(scrub(fgets(STDIN)));
            }
            while (!$attributes['module']) {
                print("\n\tA Humble project contains one or more user defined modules.  The components in this module are 'special' in that they factor in an 'Inversion of Control' paradigm that is at the core of Humble.\n");
                print("\tPlease see the training video on 'Inversion of Control' for more information.\n");
                print("\tPlease enter the module name that will contain the core (base) classes: ");
                $attributes['module']               = ucfirst(scrub(fgets(STDIN)));
            }
            while (!$attributes['namespace']) {
                print("\n\tEach module has its own internal namespace, and the components of that module are referenced using that namespace.  Note that this namespace is internal, and unlike the application level namespace which is 'Humble'\n");
                print("\tPlease enter the namespace you will be using to reference the base classes: ");
                $attributes['namespace']            = scrub(fgets(STDIN));
            }
            while (!$attributes['landing_page']) {
                print("\n\tHumble ships with a basic login page.  After logging in, you can specify where to get routed to.  Please specify that below.\n");
                print("\tPlease enter the URI for the initial landing page (i.e. /".$attributes['namespace'].'/home/page): ');
                $attributes['landing_page']            = scrub(fgets(STDIN));
            }
            $attributes['destination_folder']       = getcwd();
            @mkdir($attributes['destination_folder'],0775);
            file_put_contents('Humble.project',json_encode($attributes,JSON_PRETTY_PRINT));
            print("\n\nOk, if you got this far, you are ready to get the framework and then configure it.  Make sure your website is running before you run the next command shown below.\n\n");
            print("\n\nPlease run 'humble --fetch' to do the initial retrieval of the framework\n\n");
        } else {
            print("\nAborting creation of Humble project\n\n");
        }
    } else {
        print("\nAborting Initialization\n");
    }
}
/* ---------------------------------------------------------------------------------- */
function prepareProject() {
    $template = <<<FACTORY
<?php
    require_once "autoload.php";
    class &&FACTORY&& extends \Humble {

    }
?>
FACTORY;
    if (!file_exists('Humble.project')) {
        die("\n\n".'Project file not found.  Run "humble --init" to create the project file'."\n\n");
    }
    $project    = json_decode(file_get_contents('Humble.project'));
    $remote     = json_decode(file_get_contents($project->framework_url.'/distro/version'));
    $serial     = json_decode(file_get_contents($project->framework_url.'/distro/serialNumber?'.json_encode($project)));
    print("\n\nInstalling Humble distro version ".$remote->version." from ".$project->framework_url."\n\n");
    print("Serial Number: ".$serial->serial_number."\n\n");
    fetchProject($remote->version,$project->framework_url);
    file_put_contents('app/'.$project->factory_name.'.php',str_replace('&&FACTORY&&',$project->factory_name,$template));
    $srch = ['&&PACKAGE&&','&&MODULE&&','&&NAMESPACE&&'];
    $repl = [$project->package,$project->module,$project->namespace];
    if ($project->factory_name) {
        $srch[] = 'Humble.php';
        $repl[] = $project->factory_name.'.php';
    }
    //file_put_contents('index.php',str_replace($srch,$repl,file_get_contents('index.php')));  //replacing default Humble factory with the custom one you just created
    $srch = ['{$name}','{$version}','{$serial_number}','{$enabled}','{$polling}','{$interval}','{$installer}','{$quiescing}','{$SSO}','{$authorized}','{$idp}','{$caching}'];
    $repl = [$project->project_name,$remote->version,$serial->serial_number,1,0,15,1,0,0,0,'',1];
    file_put_contents('application.xml',str_replace($srch,$repl,file_get_contents('app/Code/Base/Humble/lib/sample/install/application.xml')));
    print("\n\n");
    print('Now running composer...'."\n");
    chdir('app');
    exec('composer install');
    $cmd = 'php Module.php --b namespace='.$project->namespace.' package='.$project->package.' module='.$project->module.' prefix='.$project->namespace.'_ '. 'author='.$project->author;
    print("\nExecuting: ".$cmd."\n\n");
    exec($cmd,$output);
    chdir('..');
    @unlink('humble.bat');
    @unlink('humble.sh');
    print("\n\nThe framework download is complete, please go to ".$project->project_url."/install.php to install your project\n\n");
    /**
     * Must write out the application.xml file
     */
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec('start '.$project->project_url.'/install.php');
    } else  {
        exec('xdg-open '.$project->project_url.'/install.php');
    }
    @unlink('Humble.php');
}
/* ---------------------------------------------------------------------------------- */
function restoreProject() {
    if (!file_exists('Humble.project')) {
        die("\n\n".'Project file not found.  Run "humble --project" to create the project file'."\n\n");
    }
    $project    = json_decode(file_get_contents('Humble.project'));
    $remote     = json_decode(file_get_contents($project->framework_url.'/distro/version'));
    print("\n\nRestoring Humble distro version ".$remote->version." from ".$project->framework_url."\n\n");
    @mkdir('tmp',0775);  //going to test extraction to this location
    $distro = 'Humble-Distro.'.$remote->version.'.zip';
    file_put_contents('tmp/'.$distro,file_get_contents($project->framework_url.'/distro/fetch'));
    print('Fetching distro from '.$project->framework_url."\n");
    $new_distro = new ZipArchive();
    if ($new_distro->open('tmp/'.$distro, ZipArchive::CREATE) !== true) {
        die('Wasnt able to open new distro');
    };
    $collision_ctr = 0; $ctr = 0;
    while (($entry = $new_distro->getFromIndex($ctr))!==false) {
        $name = $new_distro->getNameIndex($ctr);
    	if (!file_exists($name)) {
            $parts = explode('/',$name);
            if (count($parts)>1) {
		@mkdir(implode('/',array_slice($parts,0,count($parts)-1)),0775,true);
            }
            file_put_contents($name,$entry);
	} else {
            $collision_ctr++;
	}
	$ctr++;
    }
    print("Files skipped: ".$collision_ctr."\n\n\n");
    print('Now running composer...'."\n");
    chdir('app');
    exec('composer install');
    chdir('..');
    if (file_exists('humble.bat')) {
        @unlink('humble.bat');
    }
    if (file_exists('humble.sh')) {
        @unlink('humble.sh');
    }
    @unlink('Humble.php');
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec('start '.$project->project_url.'/install.php');
    } else  {
        exec('xdg-open '.$project->project_url.'/install.php');
    }
}

/* ----------------------------------------------------------------------------------
 * Main
 * ----------------------------------------------------------------------------------*/
if (PHP_SAPI === 'cli') {
    $args = array_slice($argv,1);
    if ($action = (($args && isset($args[0])) ? $args[0] : false)) {
        installedEextensionCheck();
        $action = substr($action,2);
        switch ($action) {
            case "init"     :
            case "new"      :
            case "project"  :
                initializeProject();
                break;
            case "fetch":
            case "install":
            case "prepare":
                prepareProject();
                break;
            case "restore":
                restoreProject();
                break;
            case "help" :
                print($help."\n");
                break;
            default:
                print('I do not know how to process this action: '.$action);
                break;
        }
    } else {
        print($help."\n");
    }
} else {
    print(file_get_contents('Humble.php'));
}
?>