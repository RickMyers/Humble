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
        --config      Writes apache config, needs servername= passed in
        --dockerme    Fetches a docker configuration, partially tailored
        --install     Retrieves a Humble.project file from the project hub by Serial Number
        --reregister  Gets a serial number for you application
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
/**
 * Randomly adds some spaces to the end of a word to help with the justify process
 * 
 * @param string $text
 * @param int $width
 * @return string
 */
function expandLine($text,$width) {
    $words = explode(' ',$text);
    for ($i=0; $i<($width - strlen($text)); $i++) {
        $words[rand(0,count($words)-2)] .=' ';                              //don't want to pad last word
    }
    return implode(' ',$words);
}
/**
 * HTTP Curl, i.e. "HURL"
 */
function HURL($URL,$args)	{
	$res            = null;
	$opts 			= [];
	$protocol       = (substr($URL,0,5)!=='https') ? 'ssl' : 'http';
	$content   		= http_build_query($args,'','&');
	switch ($protocol) {
		case "ssl"  :   $opts['ssl'] = [
							"verify_peer"=>false,
							"verify_peer_name"=>false,
							"crypto_method" => STREAM_CRYPTO_METHOD_ANY_SERVER
						];
						//Note, no break here, so we are going to continue and add the HTTP options below to the array
		case "http" :   $opts['http'] = [
							'header' => "Content-Type: application/x-www-form-urlencoded",
							"method" => 'POST',
							'user_agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16", /*whats a little spoofage between friends? */
							'Content-Length' => strlen($content),
							'content' => $content
						];
						break;
		default  :
						break;
	}
	$fp      		= fopen($URL,'rb',false, stream_context_create($opts));
	stream_set_timeout($fp,60000);
	if ($fp) {
		$res 		= stream_get_contents($fp);
	} else {
		print("\nError connecting with remote server: ".$URL."\n\n");
	}
	return $res;
}
/**
 * Justifies an arbitrary piece of text
 * 
 * @param string $block
 * @param int $width
 * @return string
 */
function justify($block='',$width=80) {
    $justified  = [];
    $text       = trim(str_replace(["\r","\n","\t"],['','',''],$block)); 
    $ctr        = 25;                                                       //just in case the dish runs away with the spoon... maximum 25 "lines" or iterations
    while ($text && $width && $ctr--) {
        if (($pos   = strrpos(trim(substr($text,0,$width)),' ')) && (strlen($text) > $width)) {
            $justified[] = "\t".expandLine(substr($text,0,$pos),$width);
            $text = substr($text,$pos+1);
        } else {
            $justified[] = "\t".$text;
            $text=false;
        }
    }
    return ($justified ? "\n".implode("\n",$justified).' ' : '');
}
/* ---------------------------------------------------------------------------------- */
function installedExtensionCheck() {
    exec('php -m',$modules);
    $required 		= ['json'=>false,'libxml'=>false,'mbstring'=>false,'memcache'=>false,'mongodb'=>false,'mysqli'=>false,'SimpleXML'=>false,'xml'=>false,'yaml'=>false,'zip'=>false];
	$recommended 	= ['fileinfo'=>false,'bz2'=>false,'curl'=>false,'gd'=>false,'soap'=>false];
	foreach ($recommended as $extension => $status) {
		if (!($recommend[$extension] = extension_loaded($extension))) {
			print("The extension ".$extension." is recommended but is not installed.\n");
		}
	}	
    $ok = true; $ctr = 0;
    foreach ($required as $extension => $status) {
        $ok = $ok && ($required[$extension] = extension_loaded($extension));
    }

    if (!$ok) {
        print("The following extensions should/must be enabled within your php.ini file, please consult https://www.php.net to determine how to enable them\n\n\n");
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
    print("\n".justify('Do you wish to initialize a Humble project? [yes/no]:',100));
    if (strtolower(scrub(fgets(STDIN))) === 'yes') {
        $create_project = true;
        if (file_exists('Humble.project')) {
            print(justify('A Humble project exists already, do you wish to over write? [yes/no]:',100));
           $create_project = (strtolower(scrub(fgets(STDIN))) === 'yes');
        }
        if ($create_project) {
            humbleHeader();
            $attributes     = ['project_name'=>'','project_url'=>'','factory_name'=>'','framework_url'=>'','module'=>'','namespace'=>'','package'=>'','landing_page'=>'', 'author'=>''];
            print(justify("Recommended answers to these questions are shown between the square brackets",100)."\n\n");
            while (!$attributes['framework_url']) {
                print(justify("Humble has its own framework updater but needs to know where to obtain the source from.",100)."\n");
                print(justify("Please enter the URL for the Humble source code repository [https://humbleprogramming.com]:",100));
                $attributes['framework_url']        = scrub(fgets(STDIN));
            }
            while (!$attributes['project_name']) {
                print(justify("What is the name of this project? Please enter that name here: ",100));
                $attributes['project_name']         = scrub(fgets(STDIN));
            }
            while (!$attributes['author']) {
                print(justify("What is the E-Mail address for the author of this project (i.e. you@gmail.com):",100));
                $attributes['author']               = scrub(fgets(STDIN));
            }
            while (!$attributes['project_url']) {
                print(justify("Humble has a two part installation, the first part downloads the framework, and the second part configures the framework for your project. The second part is configured using a web form, so a website (likely VHOST) has to already be created for your project.",100)."\n");
                print(justify("Please enter the URL for this project: ",100));
                $attributes['project_url']          = scrub(fgets(STDIN));
            }
            while (!$attributes['factory_name']) {
                print(justify("This is where it gets personal.  A PHP Static Factory will be created for you which will extend the primary framework's Factory class. You will reference most of the Humble framework components through this 'rebranded' Factory.  It's also a great place to keep your own Factory methods.",100)."\n");
                print(justify("Please enter the name for the rebranded main Factory class: ",100));
                $attributes['factory_name']         = scrub(fgets(STDIN));
            }
            while (!$attributes['package']) {
                print(justify("A package is nothing more than a directory.  During the creation of your application you can create as many 'packages' as you would like, here you are just creating your first.",100)."\n");
                print(justify("Please enter the package (directory) name that will contain the main project module:",100));
                $attributes['package']              = ucfirst(scrub(fgets(STDIN)));
            }
            while (!$attributes['module']) {
                print(justify("A Humble project contains one or more user defined modules.  The components in the first module are 'special' because they take part in the inheritance hierarchy for all user created components.",100)."\n");
                print(justify("Please enter the module name that will contain the core (base) classes: ",100));
                $attributes['module']               = ucfirst(scrub(fgets(STDIN)));
            }
            while (!$attributes['namespace']) {
                print(justify("Each module has its own internal namespace, and the components of that module are referenced using that namespace.  Note that this namespace is internal to the framework.",100)."\n");
                print(justify("Please enter the namespace you will be using to reference the base classes: ",100));
                $attributes['namespace']            = scrub(fgets(STDIN));
            }
            while (!$attributes['landing_page']) {
                print(justify("Humble ships with a basic login page.  After logging in, you can specify where to get routed to.  Please specify that below.",100)."\n");
                print(justify("Please enter the URI for the initial landing page [/".$attributes['namespace'].'/home/page]:',100));
                $attributes['landing_page']            = scrub(fgets(STDIN));
            }
            $attributes['destination_folder']       = getcwd();
            @mkdir($attributes['destination_folder'],0775);
			$result = json_decode(file_get_contents($attributes['framework_url'].'/distro/serialNumber?project='.urlencode(json_encode($attributes))),true);
			$attributes['serial_number'] = $result['serial_number'] ?? 'Error-Try-Again';
            file_put_contents('Humble.project',json_encode($attributes,JSON_PRETTY_PRINT));
            print(justify("Ok, if you got this far, you are ready to get the framework and then configure it.  Make sure your website is running before you run the next command shown below.\n\n",100)."\n");
            print(justify("Please run 'humble --fetch' to do the initial retrieval of the framework",100)."\n\n");
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
    
    if (!isset($project->serial_number) || !($project->serial_number)) {
        die('Please run humble --register to get your serial number before tyring to run this again');
    }
    file_put_contents('Humble.project',json_encode($project,JSON_PRETTY_PRINT));    
    print("\n\nInstalling Humble distro version ".$remote->version." from ".$project->framework_url."\n\n");
    print("Serial Number: ".$project->serial_number."\n\n");
    fetchProject($remote->version,$project->framework_url);
    file_put_contents('app/'.$project->factory_name.'.php',str_replace('&&FACTORY&&',$project->factory_name,$template));
    $srch = ['&&PACKAGE&&','&&MODULE&&','&&NAMESPACE&&'];
    $repl = [$project->package,$project->module,$project->namespace];
    if ($project->factory_name) {
        $srch[] = 'Humble.php';
        $repl[] = $project->factory_name.'.php';
    }
    //file_put_contents('index.php',str_replace($srch,$repl,file_get_contents('index.php')));  //replacing default Humble factory with the custom one you just created... bad idea maybe?
    $srch = ['{$name}','{$version}','{$serial_number}','{$enabled}','{$polling}','{$interval}','{$installer}','{$quiescing}','{$SSO}','{$authorized}','{$idp}','{$caching}'];
    $repl = [$project->project_name,$remote->version,$project->serial_number,'1','0','15','1','0','0','0','','1'];
    file_put_contents('application.xml',str_replace($srch,$repl,file_get_contents('app/Code/Base/Humble/lib/sample/install/application.xml')));
    print("\n\n");
    print('Now running composer...'."\n");
    chdir('app');
    exec('composer install');
    require('Environment.php');
    require('Humble.php');
    $location   = str_replace(["\r","\n","\m"],['','',''],((strncasecmp(PHP_OS, 'WIN', 3) === 0)) ? `where php.exe` : `which php.exe`);
    $cmd        = $location.' CLI.php --b namespace='.$project->namespace.' package='.$project->package.' module='.$project->module.' prefix='.$project->namespace.'_ '. 'author='.$project->author;
    print("\nExecuting: ".$cmd."\n\n");
    $output     = []; $rc = -99;
    exec($cmd,$output,$rc);
    print("Result: ".$rc."\nOuput Follows\n");
    print_r($output);
    chdir('..');
    @unlink('humble.bat');
    @unlink('humble.sh');
    print("\n\nThe framework download is complete, please go to ".$project->project_url."/install.php to install your project\n\n");
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
    @unlink('tmp/'.$distro);
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec('start '.$project->project_url.'/install.php');
    } else  {
        exec('xdg-open '.$project->project_url.'/install.php');
    }
}
//------------------------------------------------------------------------------
//option functions
//------------------------------------------------------------------------------
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
//------------------------------------------------------------------------------
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
//------------------------------------------------------------------------------
function loadProjectFile() {
    if (!file_exists('Humble.project')) {
        die("\n".'Run "Humble --init" first to create your project file'."\n\n");
    }	
    return json_decode(str_replace(["\r","\n"],['',''],file_get_contents('Humble.project')),true);
}
//------------------------------------------------------------------------------
function configProject($dir='',$name='localhost',$port=80,$log='') {
    if ($args    = loadProjectFile()) {
        if ($vhost = HURL($args['framework_url'].'/distro/vhost',array_merge($args,['name'=>$name,'port'=>$port,'error_log'=>$log,'current_dir'=>getcwd()]))) {
            file_put_contents('vhost.conf',$vhost);
        } else {
            die("There was a problem creating a virtual host file for you, please make sure the framework URL found in the Humble.project file is available and then try again.\n");
        }
    } else {
        die("There was a problem reading the Humble.project file, please fix and try again.\n");
    }
}
//------------------------------------------------------------------------------
function dockerMe() {
    if ($project = loadProjectFile()) {
        if ($package = HURL($project['framework_url'].'/distro/docker',$project)) {
            file_put_contents('docker_temp.zip',$package);
            $zip = new ZipArchive();
            if ($zip->open('docker_temp.zip')) {
                @mkdir('Docker/'.$project['namespace'],0775,true);
                chdir('Docker/'.$project['namespace']);
                file_put_contents('docker-compose.yaml',$zip->getFromName('docker-compose.yaml'));
                file_put_contents('docker_instructions.txt',$zip->getFromName('docker_instructions.txt'));
                chdir('Container');
                file_put_contents('dockerfile',$zip->getFromName('DockerFile'));
                file_put_contents('vhost.conf',$zip->getFromName('vhost.conf'));
                $zip->close();
                chdir('../../');
            }
            @unlink('docker_temp.zip');
            print(file_get_contents('docker_instructions.txt'));
            print("\n\n".'A docker folder has been created with a sample docker-compose and docker container definition file'."\n\n"."You should now be redirected to ".$project['framework_url']."/pages/UsingDocker.htmls for information on using Docker\n\n");
        } else {
            die("No docker package returned\n");
        }
    } else {
        die('Problem loading the Humble.project file, please fix the file and try again.'."\n");
    }
}
//------------------------------------------------------------------------------
function reregisterProject() {
	$attributes = json_decode(str_replace(["\n","\r","\m"],["","",""],file_get_contents('Humble.project')),true);
	if (isset($attributes['serial_number'])) {
		unset($attributes['serial_number']);
	}
	$result = json_decode(file_get_contents($attributes['framework_url'].'/distro/serialNumber?project='.urlencode(json_encode($attributes))),true);
	$attributes['serial_number'] = $result['serial_number'] ?? 'Error-Try-Again';	
	file_put_contents('Humble.project',json_encode($attributes,JSON_PRETTY_PRINT));
	return $attributes;
}
//------------------------------------------------------------------------------
function registerExistingProject() {
    if ($project = loadProjectFile()) {
        if ($result = HURL($project['framework_url'].'/distro/register',$project)) {
            print("\n\n".$result."\n\n");
        } else {
			print("\n\nA problem was encountered while trying to register, please try again later\n\n");
		}
    } else {
        die('Problem loading the Humble.project file, please fix the file and try again.'."\n");
    }
}
//------------------------------------------------------------------------------
function installProjectFile($serial_number=false) {
    if ($serial_number) {
        if ($json = HURL('https://humbleprogramming.com/distro/install',['serial_number'=>$serial_number])) {
            file_put_contents('Humble.project',json_encode(json_decode($json),JSON_PRETTY_PRINT));
            print("\n\nHumble.project file was installed\n\n");
        } else {
            die("\n\nFailed to retrieve Humble.project.  Installation aborted\n\n");
        }
    }
}
/* ----------------------------------------------------------------------------------
 * Main
 * ----------------------------------------------------------------------------------*/
if (PHP_SAPI === 'cli') {
    $args = array_slice($argv,1);
    if ($action = (($args && isset($args[0])) ? $args[0] : false)) {
        $args   = processArgs(array_slice($args,1));
        $action = substr($action,2);
        switch ($action) {
            case "init"     :
            case "new"      :
            case "project"  :
                initializeProject();
                break;
            case "install":
				if (!file_exists('Humble.project')) {
					if ($serial_number = fetchParameter('serial_number',$args) ? fetchParameter('serial_number',$args) : fetchParameter('sn',$args)) {
						print("\nInstalling SN:".$serial_number."\n");
						installProjectFile($serial_number);
					}
				} else {
					die("\n\nA Humble.project file already exists.  Installation aborted\n\n");
				}
				break;
            case "fetch":
            case "prepare":
                installedExtensionCheck();
                prepareProject();
                break;
			case "register":
				if (!file_exists('Humble.project')) {
					die("\n\nRun 'humble --init' to create the .project file first\n\n");
				}
				registerExistingProject();
				break;
            case "docker":
            case "dockerme":
                installedExtensionCheck();
                dockerMe();
                $project = loadProjectFile();
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    exec('start '.$project['framework_url'].'/pages/UsingDocker.htmls');
                } else  {
                    exec('xdg-open '.$project['framework_url'].'/pages/UsingDocker.htmls');
                }       				
                break;
            case "restore":
                installedExtensionCheck();
                restoreProject();
                break;
            case "cfg":
            case "conf":
            case "config":
            case "vhost":
                $name = fetchParameter('servername',$args) ? fetchParameter('servername',$args) : (fetchParameter('name',$args) ? fetchParameter('name',$args) : (fetchParameter('n',$args) ? fetchParameter('n',$args) : '')) ;
                $port = fetchParameter('port',$args)       ? fetchParameter('port',$args) : (fetchParameter('p',$args) ? fetchParameter('p',$args) : 80);
                $log  = fetchParameter('log',$args)        ? fetchParameter('log',$args)  : (fetchParameter('l',$args) ? fetchParameter('l',$args) : false);
                configProject(getcwd(),$name,$port,$log);
                print("\nA file called 'vhost.conf' has been written to the current directory.  Use that as a start to configure your Apache server\n\n ");				
                break;
			case "reregister":
				$attributes = reregisterProject();
				print_r($attributes);
				break;
            case "help" :
                print($help."\n");
                break;
            case "check" :
            case "verify":
                installedExtensionCheck();
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
