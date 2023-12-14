<?php
$help = <<<HELP
/* -----------------------------------------------------------------------------
 *  This script is used to install the base framework for ongoing enhancements
 *
 *
 *  Execution: php contribute.php
 *
 *  option:
 *      --help        This help
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


This installer is for those people who wish to contribute to the Humble project.

It will only install and set up  the base framework.  You should have downloaded
the complete  framework files,  including  internal documentation, from  the git 
repository.

This installation is not suitable for application development but rather for the
purpose of ongoing support and enhancement of the base framework.

Prerequisite: 
            
You should have PHP, MySQL, MongoDB, and optionally Memcached set up.  For MySQL
there should be a database called 'humble' created.
            
HDR;
    print($header);
}
/* ---------------------------------------------------------------------------------- */
function initializeProject() {
    print("\n".'Do you wish to continue? [yes/no]: ');
    if (strtolower(scrub(fgets(STDIN))) === 'yes') {

            $attributes     = ['MySQL'=>'','MongoDB'=>'','Memcached'=>'','User'=>''];
            while (!$attributes['MySQL']) {
                print("\n\n");
                print("Please enter the MySQL host, User ID, and Password [localhost:3306,userid,password]: ");
                $attributes['MySQL']        = scrub(fgets(STDIN));
            }
            while (!$attributes['MongoDB']) {
                print("\n\n");
                print("Please enter the MongoDB host and port, User ID, and Password and optionally port [127.0.0.1:27017,userid,password]: ");
                $attributes['MongoDB']        = scrub(fgets(STDIN));
            } 
            while (!$attributes['Memcached']) {
                print("\n\n");
                print("Please enter the Memcached host and port. If caching is not being used, please enter 'none': [127.0.0.1:11211 or none]: ");
                $attributes['Memcached']        = scrub(fgets(STDIN));
            }
            while (!$attributes['User']) {
                print("\n\n");
                print("Please enter your credentials: [User Name,Password,First Name,Last Name]: ");
                $attributes['User']        = scrub(fgets(STDIN));
            }            
            $mysql   = explode(",",$attributes['MySQL']);
            $mongodb = explode(",",$attributes['MongoDB']);
            $user    = explode(",",$attributes['User']);
            $host    = (isset($mysql[0])) ? $mysql[0] : "";
            $uid     = (isset($mysql[1])) ? $mysql[1] : "";
            $pwd     = (isset($mysql[2])) ? $mysql[2] : "";
            $db      = "humble";
            $mongo   = (isset($mongodb[0])) ? $mongodb[0] : "";
            $mongou  = (isset($mongodb[1])) ? $mongodb[1] : "";
            $mongop  = (isset($mongodb[2])) ? $mongodb[2] : "";
            $cache   = (strtolower($attributes['Memcached']) === 'none') ? '' : $attributes['Memcached'];
            $uname   = (isset($user[0])) ? $user[0] : "";
            $passwd  = (isset($user[1])) ? $user[1] : "";
            $fname   = (isset($user[2])) ? $user[2] : "";
            $lname   = (isset($user[3])) ? $user[3] : "";
            $srch    = array('&&USERID&&','&&PASSWORD&&','&&DATABASE&&','&&HOST&&','&&MONGO&&','&&CACHE&&','&&MONGOUSER&&','&&MONGOPWD&&');
            $repl    = array($uid,$pwd,$db,$host,$mongo,$cache,$mongou,$mongop);

            @mkdir('../Settings/humble',0775,true);
            @mkdir('images',0775,true);
            file_put_contents("../Settings/humble/Settings.php",str_replace($srch,$repl,file_get_contents('app/Code/Framework/Humble/lib/sample/install/Settings.php')));            
            chdir('app');
            exec('composer install');
            require "Humble.php";
            $util    = \Environment::getInstaller();
            $modules = \Environment::getRequiredModuleConfigurations();    
            foreach ($modules as $idx => $etc) {
                print('###########################################'."\n");
                print('Installing '.$etc."\n");
                print('###########################################'."\n\n");
                $util->install($etc);
            }
            $util = \Environment::getUpdater();
            foreach ($modules as $idx => $etc) {
                print('###########################################'."\n");
                print('Updating '.$etc."\n");
                print('###########################################'."\n\n");
                $util->update($etc);
            }
            $uid = Humble::entity('default/users')->setUserName($uname)->setPassword(MD5($passwd))->save();
            Humble::entity('humble/user/identification')->setId($uid)->setFirstName($fname)->setLastName($lname)->save();
            Humble::entity('humble/user/permissions')->setId($uid)->setAdmin('Y')->setSuperUser('Y')->save();
            $project = Environment::getProject();
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec('start '.$project->project_url);
            } else  {
                exec('xdg-open '.$project->project_url);
            }            
            chdir('..');
    } else {
        print("\nAborting Initialization\n");
    }
}
/* ----------------------------------------------------------------------------------
 * Main
 * ----------------------------------------------------------------------------------*/
if (PHP_SAPI === 'cli') {
    $args = array_slice($argv,1);
    if ($args && (strtolower($args[0]) == '--help')) {
        print($help);
    } else {
        humbleHeader();
        initializeProject();
    }
}
?>