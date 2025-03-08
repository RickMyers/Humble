<?php
/*------------------------------------------------------------------------------
    ____           __        ____
   /  _/___  _____/ /_____ _/ / /
   / // __ \/ ___/ __/ __ `/ / / 
 _/ // / / (__  ) /_/ /_/ / / /  
/___/_/_/_/____/\__/\__,_/_/_/   
  / ___/__________(_)___  / /_   
  \__ \/ ___/ ___/ / __ \/ __/   
 ___/ / /__/ /  / / /_/ / /_     
/____/\___/_/  /_/ .___/\__/     
                /_/              

Crude but effective
 
Since this is to install this framework, we have to use a different mechanism outside the framework to do this.

IMPORTANT:

   *** To Enable Install, edit the file application.xml file in the main modules /etc/ folder and set the value to enable (1) ***

 -------------------------------------------------------------------------------*/

function postUpdate($stage='Preparing',$step='Initializeing',$percent=0) {
    $percent = ($percent > 100) ? 100 : $percent;
    file_put_contents('../install_status.json','{ "stage": "'.$stage.'",  "step": "'.$step.'", "percent": '.$percent.' }');        
}
//------------------------------------------------------------------------------
//
function createMainModule($project) {
    print('Creating Main Module'."\n");
    $landing_page   = \Environment::getProject('landing_page');
    $location       = str_replace(["\r","\n","\m"],['','',''],((strncasecmp(PHP_OS, 'WIN', 3) === 0)) ? `where php.exe` : `which php`);
    $cmd            = $location.' CLI.php --b namespace='.$project->namespace.' package='.$project->package.' module='.$project->module.' prefix='.$project->namespace.'_ '. 'email='.$project->author.' main_module=true';
    print("\nExecuting: ".$cmd."\n\n");
    $output     = []; $rc = -99;
    exec($cmd,$output,$rc);
    print("Return Code: ".$rc."\nOuput Follows\n");
    foreach ($output as $result) {
        print($result."\n");
    }
    if (!$remote     = json_decode(file_get_contents($project->framework_url.'/distro/version'))) {
        die('Could not get current version number of the framework, check connectivity issues'."\n");
    }    
    $srch = ['{$name}','{$version}','{$serial_number}','{$enabled}','{$polling}','{$interval}','{$installer}','{$quiescing}','{$SSO}','{$authorized}','{$idp}','{$caching}','{$support_name}','{$support_email}'];
    $repl = [$project->project_name,$remote->version,$project->serial_number,'1','0','15','1','0','0','0','','1',$project->name,$project->author];
    @mkdir('Code/'.$project->package.'/'.$project->module.'/etc/',0775,true);
    file_put_contents('Code/'.$project->package.'/'.$project->module.'/etc/application.xml',str_replace($srch,$repl,file_get_contents('Code/Framework/Humble/lib/sample/install/etc/application.xml')));   
    $cmd            = $location.' CLI.php --i ns='.$project->namespace.' etc=Code/'.$project->package.'/'.$project->module.'/etc/config.xml';
   // file_put_contents('cmd1.txt',$cmd);
    exec($cmd,$output,$rc);
    print("\nExecuting: ".$cmd."\n\n");
    print("Return Code: ".$rc."\nOuput Follows\n");
    foreach ($output as $result) {
        print($result."\n");
    }  
    $cmd            = $location.' CLI.php --e ns='.$project->namespace;
    print("\nExecuting: ".$cmd."\n\n");
  //  file_put_contents('cmd2.txt',$cmd);
    exec($cmd,$output,$rc);
    print("Return Code: ".$rc."\nOuput Follows\n");
    foreach ($output as $result) {
        print($result."\n");
    }      
}
//==============================================================================

ob_start();
if (!file_exists('Humble.project')) {
    die('<h1>Missing Project File.  Run "humble --project" at the command line to create one</h1>');
}
$method     = (isset($_POST['method'])) ? $_POST['method'] : "INIT";
$docker     = file_exists('Docker/docker-compose.yaml');
$project    = json_decode(file_get_contents('Humble.project'));
$xml        = 'app/Code/'.$project->package.'/'.$project->module.'/etc/application.xml';
$xml        = file_exists($xml) ? simplexml_load_string(file_get_contents($xml)) : false;
if (!empty($xml)) {
    if (isset($xml->status)) {
        if (isset($xml->status->enabled) && ((int)$xml->status->enabled)) {

        } else {
            die("<table width='100%' height='100%'><tr><td align='center'><h1 style='color: red'>Please enable the application before attempting to install</h1></td></tr></table>");
        }
        if (isset($xml->status->installer) && ((int)$xml->status->installer)) {
            //nop; everything is good
        } else {
            die("<table width='100%' height='100%'><tr><td align='center'><h1 style='color: red'>Executing the installation script is currently disabled</h1></td></tr></table>");
        }
    } else {
        die("The application is not correctly configured.  Correct the application configuration file and try again");
    }
}
switch ($method) {
    case "mysql"    :
        $action = isset($_POST['action']) ? $_POST['action']    : false;
        $host = isset($_POST['dbhost'])   ? $_POST['dbhost']    : false;
        $uid  = isset($_POST['userid'])   ? $_POST['userid']    : false;
        $pwd  = isset($_POST['password']) ? $_POST['password']  : false;
        $db   = isset($_POST['db'])       ? $_POST['db']        : false;
        if (!$action) {
            if ($host && $uid && $db) {
                $conn = @new mysqli($host,$uid,$pwd,$db);
                if ($conn->connect_errno) {
                    die('FAILED!');
                } else {
                    die('SUCCESS');
                }
            } else {
                die('ERROR: The required fields were not passed');
            }
        }
        switch ($action) {
            case "new" :
                if ($host && $uid && $db) {
                    $conn = @new mysqli($host,$uid,$pwd);
                    if ($conn->connect_errno) {
                        die('Failed to connect to DB, check credentials and host.');
                    }
                    if ($conn->query("create database {$db}")) {
                        die('Created DB '.$db);
                    } else {
                        die('Failed creating '.$db);
                    }
                } else {
                    die('ERROR: The required fields were not passed');
                }
                break;
            default:
                break;
        }        
        die();
        break;
    case "mongo"    :
        $action = isset($_POST['action']) ? $_POST['action']    : false;
        $mongo  = isset($_POST['mongo'])   ? $_POST['mongo']    : false;
        if ($action) {
            $process    = isset($_POST['processname'])  ? $_POST['processname'] : false;
            $port       = isset($_POST['port'])         ? $_POST['port']        : false;
            $location   = isset($_POST['location'])     ? $_POST['location']    : false;
            $datadir    = isset($_POST['datadir'])      ? $_POST['datadir']     : false;
            switch ($action) {
                case "new"      :
                    $rc = @mkdir($datadir,0775,true);
                    if ($rc) {
                        @mkdir($datadir.'/log',0775,true);
                        @mkdir($datadir.'/data',0775,true);
                        $message = [
                           'rc' => 0,
                           'txt' => 'Unable to create the directory: '.$datadir
                        ];
                        die(json_encode($message));
                    }
                    $cfg = <<<CONFIG
systemLog:
    destination: file
    path: {$datadir}\log\mongo.log
storage:
    dbPath: {$datadir}\data
net:
   bindIp: 127.0.0.1
   port: {$port}
CONFIG;
                    file_put_contents($datadir.'/mongod.cfg',$cfg);
                    $cmd = <<<CMD
    sc.exe create {$process} binPath= "\"{$location}\" --service --config=\"{$datadir}\mongod.cfg\"" DisplayName= "{$process}" start= "auto"
    CMD;
                    $message = [
                        'rc'  => 1,
                        'txt' => 'To create the instance, you will need to copy and paste the line below into a windows command terminal that has administrator privileges',
                        'cmd' => $cmd
                    ];
                    die(json_encode($message));
                    break;
                default         :
                    break;
            }
        } else if ($mongo) {
            chdir('app');
            require 'vendor/autoload.php'; // include Composer's autoloader

            $client = new MongoDB\Client("mongodb://".$mongo);

            try {
                $dbs = $client->listDatabases();
            }
            catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e)   {
                die('FAILED TESTING MONGO CONNECTION!');
            }
            die('SUCCESSFLY CONNECTED TO MONGODB');
        } else {
            die('ERROR: The required fields were not passed');
        }        
        
        die();
        break;
    default:
        break;
}

?>
<!--!DOCTYPE html-->
<html>
    <head>
        <link rel='stylesheet' type='text/css' href='/web/css/index.css' />
        <style type="text/css">
            /* url(/images/paradigm/bg_graph.png)*/
            body {
                height: 100%; box-sizing: border-box;  background-image: url(/web/images/bg_graph.png);
            }
            body, div, table {
                margin: 0px; padding: 0px; border: 0px; position: relative
            }
            fieldset {
                margin-bottom: 10px; padding: 12px 15px;
            }
            legend {
                font-size: .8em; font-family: sans-serif
            }
            .installer-field-description {
                font-size: .6em; font-family: sans-serif; margin-bottom: 10px
            }
            .installer-form-field {
                border-radius: 3px; padding: 3px 4px; border: 1px solid #aaf; background-color: #eee; width: 70%
            }
            .installer-form-field:focus {
                background-color: white
            }
            .installer-form-div {
                width: 705px; padding: 20px 30px; border: 1px solid #aaf; border-top: 0px; background-color: #e0e0e0;
            }
            .installer-form-tabs {
                width: 705px; padding: 20px 30px 0px 30px; border: 1px solid #aaf; border-top: 0px; background-color: #e0e0e0;
            }            
        </style>
        <script type="text/javascript" src="/web/js/jquery.js"></script>
        <script type="text/javascript" src="/web/js/EasyAjax.js"></script>
        <script type="text/javascript" src="/web/js/EasyTabs.js"></script>
        <script type="text/javascript" src="/web/js/EasyEdit.js"></script>
        <script type="text/javascript">
            var Installer = {
                width: 0,
                height: 0,
                init: function () {
                    Installer.resize();
                },
                start: function () {
                    window.setTimeout(Installer.update,500);
                },
                update: function () {
                    (new EasyAjax('/install_status.json')).then(function (response) {
                        var progress = JSON.parse(response);
                        console.log(progress);
                        if (progress) {
                            $('#install-status-stage').html(progress.stage);
                            $('#install-status-step').html(progress.step);
                            $('#install-status-bar').css('width',progress.percent+'%');
                            if (progress.percent < 100) {
                                window.setTimeout(Installer.update,750);
                            }
                        }
                    }).get();
                },
                resize: function () {
                        Installer.width = window.innerWidth ||
                            document.documentElement.clientWidth ||
                            document.body.clientWidth;
                        Installer.height = window.innerHeight ||
                            document.documentElement.clientHeight ||
                            document.body.clientHeight;
                        $("#installer-area").css('height', Installer.height + "px");
                }
            }
            window.onload   = Installer.init;
            window.onresize = Installer.resize;
        </script>
    </head>
    <body>
<?php
//----------------------------------------------------------------------------------------------------------------

$info = [
    'User' => [
        'First' => '',
        'Last'  => '',
        'User'  => ''
    ],
    'MySQL' => [
        'Host' => '127.0.0.1:3306',
        'User' => '',
        'Password' => '',
        'Database'  => ''
        ],
    'MongoDB' => [
        'Host' => '127.0.0.1:27017',
        'User' => '',
        'Password' => '',                
    ]
];
switch ($method) {
    case "INIT"         :
        if ($docker) {
            $settings = yaml_parse_file('Docker/docker-compose.yaml');
            $name     = explode(' ',(string)$project->name);
            $ns       = $project->namespace;
            $info = [
                'User' => [
                    'First' => $name[0] ?? '',
                    'Last'  => $name[1] ?? '',
                    'Email' => $project->author ?? '',
                    'ID'    => strtolower((substr($name[0],0,1).$name[1]))
                ],
                'MySQL' => [
                    'Host'      => $settings['services'][$ns.'_mysql']['container_name'] ?? '',
                    'User'      => $settings['services'][$ns.'_mysql']['environment']['MYSQL_USER'] ?? '',
                    'Password'  => $settings['services'][$ns.'_mysql']['environment']['MYSQL_PASSWORD'] ?? '',
                    'Database'  => $settings['services'][$ns.'_mysql']['environment']['MYSQL_DATABASE'] ?? ''
                    ],
                'MongoDB' => [
                    'Host'      => $settings['services'][$ns.'_mongodb']['container_name'] ?? '',
                    'User'      => '',
                    'Password'  => '',                
                ]
            ];
        }        
        ?>

            <div id="installation-data">
            <table id="installer-area" style="width: 100%; height: 100%" cellspacing='0' cellpadding='0'>
                <tr style="height: 20px">
                    <td>
                        <div class="flat-brick" style="background-color: #0F3F3F"></div>
                        <div style="clear: both"></div>
                    </td>
                </tr>
                <tr>
                    <td align='center' valign='middle'>
                        <div id="installer-wait-div" style="display: none">
                            <div id="install-status-stage" style="font-size: 2em; color: #333; font-family: sans-serif; margin-bottom: 15px">
                                &nbsp;
                            </div>
                            <img src="/web/images/wait.gif" alt="Please Wait..." style="height: 120px" />
                            <div style="margin-right: auto; margin-left: auto; height: 30px; width: 400px; position: relative; border: 1px solid #333; text-align: left; margin-top: 15px">
                                <div id="install-status-bar" style="width: 30%; height: 100%; background-color: rgba(80,80,80,.5)">

                                </div>
                                <div id="install-status-step" style="position: absolute; top: 6px; left: 4px; color: blue; font-family: sans-serif;">
                                    &nbsp;
                                </div>

                            </div>
                        </div>
                        <div id="installer-tabs" class='installer-form-tabs' style="height: auto; padding: 0px 30px; border: 1px solid #aaf; border-bottom: 0px;"></div>
                        <form name='installer-form' method='post' id='installer-form' onsubmit="return false" action="" style="margin: 0px; border: 0px; font-family: sans-serif;">
                            <div class='installer-form-div' id="installer-form-div" style='text-align: left; position: relative; display: block;'>
                                <div style="padding: 10px; color: white; font-size: 1em; font-family: sans-serif; margin-bottom: 20px; text-align: center; background-color: #0F3F3F">
                                    Welcome to the Installation for <?=$project->project_name?>
                                </div>

                                <input type="hidden" name="method" id="method" value="INSTALL" />
                                <input type="hidden" name="serial_number" id="serial_number" value="<?=$project->serial_number?>" />

                                <fieldset style="float: left; width: 250px; margin: 0px 4px 0px 0px" id="div_1"><legend>Administrator Information</legend>
                                    <div class='installer-field-description'>Serial Number: <b><?=$project->serial_number?></b></div>
                                    <input type='text' class='installer-form-field' id='email' name='email' value="<?=$info['User']['Email']?>" />
                                    <div class='installer-field-description'>E-Mail</div>

                                    <input type='text' class='installer-form-field' id='username' name='username' value="<?=$info['User']['ID']?>" />
                                    <div class='installer-field-description'>User Login Id</div>

                                    <input type='password' class='installer-form-field' id='pwd' name='pwd' />
                                    <div class='installer-field-description'>Password</div>

                                    <input type='password' class='installer-form-field' id='confirm' name='confirm' />
                                    <div class='installer-field-description'>Confirm Password</div>

                                    <input type='text' class='installer-form-field' id='firstname' name='firstname'  value="<?=$info['User']['First']?>" />
                                    <div class='installer-field-description'>First Name</div>

                                    <input type='text' class='installer-form-field' id='lastname' name='lastname'  value="<?=$info['User']['Last']?>" />
                                    <div class='installer-field-description'>Last Name</div>
                                    <input type="button" id="install-submit" name="install-submit" value=" Install " />
                                </fieldset>

                                <fieldset style="display: inline-block; width: 350px; position: relative; margin: 0px" id="div_2"><legend>Database Information</legend>
                                    <input type='text' placeholder="127.0.0.1:3306" class='installer-form-field' id='dbhost' name='dbhost'  value="<?=$info['MySQL']['Host']?>" /><input type='button' value=' Test ' id='install-test' name='install-test' />
                                    <div class='installer-field-description'>MySQL Host (localhost:port or leave out port for default)</div>

                                    <input type='text' class='installer-form-field' id='db' name='db'  value="<?=$info['MySQL']['Database']?>"/>
                                    <div class='installer-field-description'>MySQL Database Name</div>

                                    <input type='text' class='installer-form-field' id='userid' name='userid' value="<?=$info['MySQL']['User']?>" />
                                    <div class='installer-field-description'>MySQL User Id</div>

                                    <input type='password' class='installer-form-field' id='password' name='password' value="<?=$info['MySQL']['Password']?>" />
                                    <div class='installer-field-description'>MySQL Password</div>

                                    <input type='text' placeholder="127.0.0.1:27017" class='installer-form-field' id='mongo' name='mongo' value="<?=$info['MongoDB']['Host']?>"/><input type="button" value=" Test " id='mongo-test' name='mongo-test' />
                                    <div class='installer-field-description'>MongoDB Host</div>

                                    <input type='text' class='installer-form-field' id='mongo_userid' name='mongo_userid' />
                                    <div class='installer-field-description'>MongoDB User Id</div>

                                    <input type='text' class='installer-form-field' id='mongo_password' name='mongo_password' />
                                    <div class='installer-field-description'>MongoDB Password</div>

                                    <div style="position: relative">
                                        <select id="cache" name="cache" class='installer-form-field'>
                                            <option value=""> </option>
                                            <option value="127.0.0.1:11211" <?php if ($docker) { print('selected'); }?>> Memcache [127.0.0.1:11211]</option>
                                            <option value="127.0.0.1:6379"> Redis [127.0.0.1:6379]</option>
                                        </select>
                                        <input type="text" id="cache_combo" name="cache_combo" />
                                    </div>
                                    <div class='installer-field-description'>Cache Server (Memcache/Redis) </div>
                                </fieldset>
                                <div style="clear: both"></div>
                            </div>
                            <div id="installer-options" class='installer-form-div' style="display: flex; flex-direction: column; justify-content: left; padding-left: 10px ">
                                <fieldset><legend>Installation Options</legend>
                                    <label for="templater">Default Templater: </label>
                                    <select name="templater" id="templater">
                                        <option value="Twig"> Twig </option>
                                        <option value="Smarty" selected="true"> Smarty </option>
                                        <option value="Latte"> Latte </option>
                                        <option value="Blade"> Blade </option>
                                        <option value="Savant"> Savant </option>
                                        <option value="TBS"> Tiny But Strong </option>
                                        <option value="PHP Tal"> PHPTal </option>
                                        <option value="Rain"> Rain </option>
                                        <option value="Mustache"> Mustache </option>
                                        <option value="PHP"> PHP </option>
                                    </select><br /><br />
                                    <label for="landing-default">Landing Page: </label>
                                    <input type="radio" name="landing" id="landing-default" checked="checked" value="default" /> Default
                                    <input type="radio" name="landing" id="landing-enhanced" value="enhanced" /> Enhanced (alpha)<br /><br />
                                    <input type="checkbox" name="authorization_engine" id="authorization_engine" value="Y" /> Include Basic Authorization Engine<br /><br />
                                    <input type="checkbox" name="roles_and_relationships" id="roles_and_relationships" value="Y" /> Include Roles And Relationships Features<br /><br />
                                    <input type="checkbox" name="socket_server" id="socket_server" value="Y" /> Install Socket Server (Node.js/NPM Required)<br /><br />
                                    Admin App Name: <input type="text" name="app_name" id="app_name" value="app" /><br /><br />
                                    
                                </fieldset>
                            </div>                        
                        </form>
                        <div id="installer-new-db" class='installer-form-div'>
                            <table style="width: 100%; height: 100%"><tr><td>
                                <form name="new-db-form" id="new-db-form" onsubmit="return false">
                                    <fieldset style="padding: 10px 10px; font-family: sans-serif; font-size: .9em"><legend>New MySQL DB</legend>
                                        If you haven't already created a DB (required), you can do that here.<br /><br />
                                        <input type='text' name='host' id='rdms-host' class='installer-form-field' value="<?=$info['MySQL']['Host']?>" /><br />
                                        <div class='installer-field-description'>Host:Port</div>
                                        <input type='text' name='userid' id='rdms-userid' class='installer-form-field' value="<?=$info['MySQL']['User']?>" /><br />
                                        <div class='installer-field-description'>User ID</div>
                                        <input type='password' name='rdms-password' id='rdms-password' class='installer-form-field' value="<?=$info['MySQL']['Password']?>" /><br />
                                        <div class='installer-field-description'>Password</div>
                                        <input type='text' name='db' id='rdms-db' class='installer-form-field' value="<?=$info['MySQL']['Database']?>" /><br />
                                        <div class='installer-field-description'>New Database</div>
                                        <input type='button' name='create-db-button' id='create-db-button' value=' Create Database ' /><br />
                                    </fieldset>
                                </form></td></tr>
                            </table>
                        </div>
                        <div id="installer-new-mongodb" class='installer-form-div'>
                            <table style="width: 100%; height: 100%"><tr><td>
                                <form name="new-mongodb-form" id="new-mongodb-form" onsubmit="return false">
                                    <fieldset style="padding: 10px 10px; font-family: sans-serif; font-size: .9em"><legend>New NoSQL MongoDB Instance</legend>
                                        This only applies if you are on Windows and not using our Docker provided images. You can create a new MongoDB instance here, assuming you already have it installed. 
                                        In this way, you can have a unique instance of MongoDB per application running on this machine (really, just use our Docker tools and Fuhgeddaboudit)<br /><br />
                                        <input type='text' name='datadir' id='mongo-datadir' class='installer-form-field' value="" /><br />
                                        <div class='installer-field-description'>Data Directory</div>
                                        <input type='text' name='processname' id='mongo-processname' class='installer-form-field' value="" /><br />
                                        <div class='installer-field-description'>Process Name</div>
                                        <input type='text' name='port' id='mongo-port' class='installer-form-field' value="27017" placeholder="27017" /><br />
                                        <div class='installer-field-description'>Mongo Port</div>
                                        <input type='text' name='location' id='mongo-location' class='installer-form-field' value="c:\Program Files\MongoDB\Server\3.2\bin\mongod.exe"  /><br />
                                        <div class='installer-field-description'>Mongo Path (correct the above path)</div><br />
                                        <input type='text' name='cmd' id='mongo-cmd' class='installer-form-field' value="" placeholder="You will need to run this statment at an elevated command prompt"  /><br />
                                        <div class='installer-field-description'>Create Mongo Instance Command</div><br /><br />
                                        <input type='button' name='create-mongodb-button' id='create-mongodb-button' value=' Create Mongo Instance ' /><br />
                                    </fieldset>
                                </form></td></tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr style="height: 20px">
                    <td>
                        <div class="flat-brick" style="background-color: #0F3F3F"></div>
                        <div style="clear: both"></div>
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
                (function () {
                    new EasyEdits('/web/edits/newdb.json','new-db');
                    new EasyEdits('/web/edits/newmongodb.json','new-mongodb');
                    new EasyEdits('/web/edits/install.json','install-form');
                    $('#div_1').height($('#div_2').height());
                    var tabs = new EasyTab('installer-tabs',125);
                    tabs.add('Installation',null,'installer-form-div');
                    tabs.add('New RDMS DB',null,'installer-new-db');
                    tabs.add('New MongoDB',null,'installer-new-mongodb');
                    tabs.add('Install Options',null,'installer-options');
                    tabs.tabClick(0);
                    $('.installer-form-div').css('height',$('#installer-form').css('height'));
                })();
            </script>
            </div>

        <?php
        break;
    case "INSTALL"      :
        ob_start();
        $project = json_decode(file_get_contents('Humble.project'));
        $step   = 0;
        $email  = isset($_POST['email'])            ? $_POST['email']           : $project->author;
        $host   = isset($_POST['dbhost'])           ? $_POST['dbhost']          : false;
        $uid    = isset($_POST['userid'])           ? $_POST['userid']          : false;
        $pwd    = isset($_POST['password'])         ? $_POST['password']        : false;
        $upwd   = isset($_POST['pwd'])              ? $_POST['pwd']             : false;
        $mongo  = isset($_POST['mongo'])            ? $_POST['mongo']           : false;
        $mongou = isset($_POST['mongo_userid'])     ? $_POST['mongo_userid']    : false;
        $mongop = isset($_POST['mongo_password'])   ? $_POST['mongo_password']  : false;
        $serial = isset($_POST['serial_number'])    ? $_POST['serial_number']   : false;
        $db     = isset($_POST['db'])               ? $_POST['db']              : false;
        $cache  = isset($_POST['cache'])            ? $_POST['cache']           : false;
        $redis  = strpos($cache,'6379');
        $fname  = isset($_POST['firstname'])        ? $_POST['firstname']       : '';
        $lname  = isset($_POST['lastname'])         ? $_POST['lastname']        : '';
        $use    = isset($_POST['templater'])        ? $_POST['templater']       : 'Smarty';
        $app    = isset($_POST['app_name'])         ? $_POST['app_name']        : 'app';
        $srch   = array('&&USERID&&','&&PASSWORD&&','&&DATABASE&&','&&HOST&&','&&MONGO&&','&&CACHE&&','&&MONGOUSER&&','&&MONGOPWD&&');
        $repl   = array($uid,$pwd,$db,$host,$mongo,$cache,$mongou,$mongop);
        

        $registration_data = [
            'serial_number' => $serial,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'email'         => $email,
            'project'       => $project->project_name,
            'project_url'   => $project->project_url,
            'factory_name'  => $project->factory_name
        ];

        $context = stream_context_create(['http'=>['method'=>'POST','header'=>'Content-type: application/json' ,'content'=>json_encode($registration_data)]]);
        $response = file_get_contents($project->framework_url.'/account/registration/activation',false,$context);    
        
        @mkdir('../Settings/'.$project->namespace,0775,true);
        @mkdir('images',0775,true);
        file_put_contents("../Settings/".$project->namespace."/Settings.php",str_replace($srch,$repl,file_get_contents('app/Code/Framework/Humble/lib/sample/install/Settings.php')));
        chdir('app');
        postUpdate('Preparing','Initializing',0);
        require_once('Humble.php');
        $util            = \Environment::getInstaller();
        $modules         = \Environment::getRequiredModuleConfigurations();
        $percent         = 100/((count($modules)+1)*2);                         //2 steps per module, plus we will be creating a new module in this process
        postUpdate('Starting','Building Application Module',(++$step*$percent));
        print('<pre>');
        foreach ($modules as $idx => $etc) {
            postUpdate('Installing','Installing '.$etc,(++$step*$percent));
            print('###########################################'."\n");
            print('Installing '.$etc."\n");
            print('###########################################'."\n\n");
            $util->install($etc);
        }
        //======================================================================

        postUpdate('Building','Activating Application Module',(++$step*$percent));
        createMainModule($project);
        
        //======================================================================
        $custom = 'Code/'.$project->package.'/'.$project->module.'/etc/Constants.php'; 
        if (($redis) && (file_exists($custom))) {                               //You chose REDIS for cache, so going to uncomment out the line in the Custom.php file
            foreach ($lines = explode("\n",$custom) as $idx => $line) {
                if (strpos($line,'$USE_REDIS') && (substr($line,0,2)=='//')) {
                    $lines[$idx] = substr($line,2);
                }
            }
            file_put_contents($custom,implode("\n",$lines));
        }
        
        $admin_id  = \Humble::entity('admin/users')->newUser($_POST['username'],MD5($upwd),$fname,$lname,$email);
        
        $install_manager = \Humble::model('humble/manager');        
        $install_manager->tailorSystem($project);                               //We are going to have to copy a model and a controller into the new module to handle logging in        
        $install_manager->setAdminId($admin_id)->setDescription('Homepage Controller')->setActionDescription('The Home Page')->createLandingPage($project);
        
        //
        // ###NOW RUN UPDATE ON EACH MODULE!!!!#######
        //
        $util = \Environment::getUpdater();
        foreach ($modules as $idx => $etc) {
            postUpdate('Updating','Updating '.$etc,(++$step*$percent));
            print('###########################################'."\n");
            print('Updating '.$etc."\n");
            print('###########################################'."\n\n");
            $util->update($etc);
        }
        $util->update('Code/'.$project->package.'/'.$project->module.'/etc/config.xml');

        postUpdate('Finalizing','Registering Administrator',(++$step*$percent));
        //need to create the user tables... should do that
        $user_id      = \Humble::entity('default/users')->newUser($_POST['username'],MD5($upwd),$fname,$lname,$email);        
        $util->disable();                                                       //Disabling the installer to prevent accidental re-run
        
        $results      = ob_get_flush();
        if (!$user_id) {
            file_put_contents('install_failed.txt',$results);
            print('<pre>'.$results."\n\n\n".'</pre>');
            die('Install did not complete, no user was created'."\n");
        } 
        
        session_start();
        $_SESSION['uid'] = $user_id;
        print('Attempting to create drivers'."\n");
        $linux_sh = strtolower((string)$project->factory_name);
        @copy('humble.bat',strtolower((string)$project->factory_name).'.bat');
        @copy('humble',$linux_sh);
        @chmod($linux_sh,0775);
        $x = (file_exists('../Humble.php')) ? @unlink('../Humble.php') : '';
        $x = (file_exists('../humble.bat')) ? @unlink('../humble.bat') : '';
        $x = (file_exists('Humble.bat'))    ? @unlink('Humble.bat') : '';
        $x = (file_exists('../humble'))     ? @unlink('../humble') : '';
        $x = (file_exists('humble'))        ? @unlink('humble') : '';
        file_put_contents($linux_sh,str_replace("\r","",file_get_contents($linux_sh)));
        if (file_exists('../.htaccess')) {
            $parts  = explode('/',$project->landing_page);
            $srch   = ['&&NAMESPACE&&','&&PACKAGE&&','&&MODULE&&','&&CONTROLLER&&','&&PAGE&&'];
            $repl   = [$project->namespace,$project->package,$project->module,$parts[2],$parts[3]];
            file_put_contents('../.htaccess',str_replace($srch,$repl,file_get_contents('../.htaccess')));
        }
        print("done with creating drivers\n\n");
        $log = ob_get_flush();
        //if error, then print log
        //print('<html><body><textarea style="width: 100%; height: 100%">'.$log.'</textarea></body></html>');
        postUpdate('Complete','Finished',100);
        file_put_contents('../install.log',$log);
        @unlink('../install_status.json');
        //no create the admin app
        $cmd = 'php CLI.php --admin-apps ns='.$project->namespace.' nm='.$app;
        postUpdate('Creating Admin App');
        exec($cmd,$results,$rc);
        print('Admin App Results: '.$rc."\n");
        print_r($results);
        ?>
        <script>
            window.location.href = '/index.html?message=Installation Completed, Please Login...';
        </script>
        <?php
        break;
    default             :
        die("I'm not sure what you want, but I'm pretty sure I don't do that\n");
        break;
}
?>
    </body>
</html>