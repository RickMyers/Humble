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
function restoreProject() {
    if (!file_exists('Humble.project')) {
        die("\n\n".'Project file not found.  Run "humble --project" to create the project file'."\n\n");
    }
    $project    = json_decode(file_get_contents('Humble.project'));
    $remote     = json_decode(file_get_contents($project->framework_url.'/distro/version'));
    print('Fetching distro from '.$project->framework_url."\n");
    print("\n\n");
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
        $action = substr($action,2);
        switch ($action) {
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

}
?>