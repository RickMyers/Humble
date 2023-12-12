<?php
    ob_start();
    function postUpdate($stage='Preparing',$step='Initializeing',$percent=0) {
        file_put_contents('../install_status.json','{ "stage": "'.$stage.'",  "step": "'.$step.'", "percent": '.$percent.' }');        
    }
?>
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
//Since this is to install this framework, we have to use a different mechanism outside the framework to do this.
//
//IMPORTANT:
//
//   *** To Enable Install, edit the file application.xml and set the value to enable ***
//
//----------------------------------------------------------------------------------------------------------------
$data = "";
$data = (file_exists('application.xml')) ? file_get_contents('application.xml') : die("Install is not possible at this time.");
$xml  = simplexml_load_string($data);

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
} else {
    die("There is an error in the application configuration file");
}
$method     = (isset($_POST['method'])) ? $_POST['method'] : "INIT";
$project    = json_decode(file_get_contents('Humble.project'));
$docker     = file_exists('Docker/'.$project->namespace.'/docker-compose.yaml');
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
            $settings = yaml_parse_file('Docker/'.$project->namespace.'/docker-compose.yaml');
            $name     = explode(' ',(string)$project->name);
            $info = [
                'User' => [
                    'First' => $name[0] ?? '',
                    'Last'  => $name[1] ?? '',
                    'Email' => $project->author ?? '',
                    'ID'    => strtolower((substr($name[0],0,1).$name[1]))
                ],
                'MySQL' => [
                    'Host'      => $settings['services']['mysql']['container_name'] ?? '',
                    'User'      => $settings['services']['mysql']['environment']['MYSQL_USER'] ?? '',
                    'Password'  => $settings['services']['mysql']['environment']['MYSQL_PASSWORD'] ?? '',
                    'Database'  => $settings['services']['mysql']['environment']['MYSQL_DATABASE'] ?? ''
                    ],
                'MongoDB' => [
                    'Host'      => $settings['services']['mongodb']['container_name'] ?? '',
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
                        <div id="installer-tabs" class='installer-form-div' style="height: auto; padding: 0px 30px; border: 1px solid #aaf; border-bottom: 0px;"></div>
                        <div class='installer-form-div' id="installer-form-div" style='text-align: left; position: relative; display: block;'>
                            <div style="padding: 10px; color: white; font-size: 1em; font-family: sans-serif; margin-bottom: 20px; text-align: center; background-color: #0F3F3F">
                                Welcome to the Installation for <?=$xml->name?>
                            </div>
                            <form name='installer-form' method='post' id='installer-form' onsubmit="return false" action="">
                                <input type="hidden" name="method" id="method" value="INSTALL" />
                                <input type="hidden" name="serial_number" id="serial_number" value="<?=$project->serial_number?>" />

                                <fieldset style="float: left; width: 250px; position: relative" id="div_1"><legend>Administrator Information</legend>
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

                                <fieldset style="display: inline-block; width: 350px; position: relative" id="div_2"><legend>Database Information</legend>
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

                                <input type='text' placeholder="127.0.0.1:11211" class='installer-form-field' id='cache' name='cache' value="<?php if ($docker) { print('127.0.0.1:11211'); }?>"/>
                                <div class='installer-field-description'>Memcached Server </div>
                                </fieldset>
                                <div style="clear: both"></div>
                            </form>
                        </div>
                        <div id="installer-new-db" class='installer-form-div'>
                            <table style="width: 100%; height: 100%"><tr><td>
                                <form name="new-db-form" id="new-db-form" onsubmit="return false">
                                    <fieldset style="padding: 10px 10px; font-family: sans-serif; font-size: .9em"><legend>New MySQL DB</legend>
                                        If you haven't already created a DB (required), you can do that here.<br /><br />
                                        <input type='text' name='host' id='rdms-host' class='installer-form-field' value="" /><br />
                                        <div class='installer-field-description'>Host:Port</div>
                                        <input type='text' name='userid' id='rdms-userid' class='installer-form-field' value="" /><br />
                                        <div class='installer-field-description'>User ID</div>
                                        <input type='text' name='password' id='rdms-password' class='installer-form-field' value="" /><br />
                                        <div class='installer-field-description'>Password</div>
                                        <input type='text' name='db' id='rdms-db' class='installer-form-field' value="" /><br />
                                        <div class='installer-field-description'>New Database</div>
                                        <input type='button' name='create-db-button' id='create-db-button' value=' Create DB ' /><br />
                                    </fieldset>
                                </form></td></tr>
                            </table>
                        </div>
                        <div id="installer-new-mongodb" class='installer-form-div'>
                            <table style="width: 100%; height: 100%"><tr><td>
                                <form name="new-mongodb-form" id="new-mongodb-form" onsubmit="return false">
                                    <fieldset style="padding: 10px 10px; font-family: sans-serif; font-size: .9em"><legend>New NoSQL MongoDB Instance</legend>
                                        You can create a new MongoDB instance here, assuming you already have it installed.  In this way, you can have a unique
                                        instance of MongoDB (recommended) per application running on this machine<br /><br />
                                        <input type='text' name='datadir' id='mongo-datadir' class='installer-form-field' value="" /><br />
                                        <div class='installer-field-description'>Data Directory</div>
                                        <input type='text' name='processname' id='mongo-processname' class='installer-form-field' value="" /><br />
                                        <div class='installer-field-description'>Process Name</div>
                                        <input type='text' name='port' id='mongo-port' class='installer-form-field' value="" placeholder="27017" /><br />
                                        <div class='installer-field-description'>Mongo Port</div>
                                        <input type='text' name='location' id='mongo-location' class='installer-form-field' value="c:\Program Files\MongoDB\Server\3.2\bin\mongod.exe"  /><br />
                                        <div class='installer-field-description'>Mongo Port</div><br />
                                        <input type='text' name='cmd' id='mongo-cmd' class='installer-form-field' value="" placeholder="You will need to run this statment at an elevated command prompt"  /><br />
                                        <div class='installer-field-description'>Create Mongo Instance Command</div><br /><br />
                                        <input type='button' name='create-mongodb-button' id='create-mongodb-button' value=' Create Mongo Instance ' /><br />
                                    </fieldset>
                                </form></td></tr>
                            </table>
                        </div>
                        <div id="installer-options" class='installer-form-div'>
                            options go here (choice of templater, whether to include default landing)
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
                })();
            </script>
            </div>

        <?php
        break;
    case "INSTALL"      :
        ob_start();
        $step   = 0;
        postUpdate('Preparing','Initializing',0);
        $email  = isset($_POST['email'])            ? $_POST['email']           : false;
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
        $fname  = isset($_POST['firstname'])        ? $_POST['firstname']       : '';
        $lname  = isset($_POST['lastname'])         ? $_POST['lastname']        : '';
        $srch   = array('&&USERID&&','&&PASSWORD&&','&&DATABASE&&','&&HOST&&','&&MONGO&&','&&CACHE&&','&&MONGOUSER&&','&&MONGOPWD&&');
        $repl   = array($uid,$pwd,$db,$host,$mongo,$cache,$mongou,$mongop);
        if (!file_exists('Humble.project')) {
            die('<h1>Missing Project File.  Run "humble --project" at the command line to create one</h1>');
        }
        $registration_data = [
            'serial_number' => $serial,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'email'         => $email,
            'project'       => $project->project_name,
            'project_url'   => $project->project_url,
            'factory_name'  => $project->factory_name
        ];

//        $context = stream_context_create(['http'=>['method'=>'POST','header'=>'Content-type: application/json' ,'content'=>json_encode($registration_data)]]);
//        $response = file_get_contents($project->framework_url.'/account/registration/activation',false,$context);    
        
        @mkdir('../Settings/'.$project->namespace,0775,true);
        @mkdir('images',0775,true);
        file_put_contents("../Settings/".$project->namespace."/Settings.php",str_replace($srch,$repl,file_get_contents('app/Code/Base/Humble/lib/sample/install/Settings.php')));
        chdir('app');
        require_once('Humble.php');
        $util    = \Environment::getInstaller();
        $modules = \Environment::getRequiredModuleConfigurations();
        $percent = 100/((count($modules)+1)*2);                                 //2 steps per module, plus we will be creating a new module in this process
        postUpdate('Starting','Building Application Module',(++$step*$percent));
        
        Humble::model('humble/manager')->tailorSystem();                     //We are going to have to copy a model and a controller into the new module to handle logging in
        
        foreach ($modules as $idx => $etc) {
            postUpdate('Installing','Installing '.$etc,(++$step*$percent));
            print('###########################################'."\n");
            print('Installing '.$etc."\n");
            print('###########################################'."\n\n");
            $util->install($etc);
        }
        
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

        

        //This fakes out the CLI to thinking it was called at the command line
        $args = [
            "CLI.php",
            "--activate",
            "namespace=".$project->namespace,
            "package=".$project->package,
            "module=".$project->module
        ];
        
        postUpdate('Finalizing','Activating Application Module',(++$step*$percent));
        include "CLI.php";        
        
        postUpdate('Finalizing','Registering Administrator',(++$step*$percent));
        $landing_page = (string)str_replace("\\","",$project->landing_page);
        $landing      = explode('/',$landing_page);
        $ins          = Humble::model('humble/utility');
        $uid          = \Humble::entity('admin/users')->newUser($_POST['username'],MD5($upwd),$fname,$lname,$email);
        $util->disable();                                                       //Disabling the installer to prevent accidental re-run
        ob_start();
        
        $results      = ob_get_flush();
        if (!$uid) {
            file_put_contents('install_failed.txt',$results);
            print(file_get_contents('install_failed.txt')."\n");
            die('Install did not complete, no admin user was created'."\n");
        } 
        $ins->setId($uid)->setNamespace($project->namespace)->setEngine('Smarty3')->setName($landing[2])->setAction($landing[3])->setDescription('Basic Controller')->setActionDescription('The Home Page')->createController(true);
        session_start();
        $_SESSION['uid'] = $uid;
        print('Attempting to create drivers'."\n");
        print(getcwd()."\n");
        @copy('../humble.bat',strtolower((string)$project->factory_name).'.bat');
        @copy('../humble.sh',strtolower((string)$project->factory_name).'.sh');
        $x = (file_exists('../humble.bat')) ? @unlink('../humble.bat') : '';
        $x = (file_exists('../humble.sh'))  ? @unlink('../humble.sh') : '';
        $x = (file_exists('../Humble.php')) ? @unlink('../Humble.php') : '';
        print("done with creating drivers\n\n");
        $log = ob_get_flush();
        print($log);
        postUpdate('Complete','Finished',100);
        file_put_contents('../install.log',$log);
        break;
    default             :
        die("I'm not sure what you want, but I'm pretty sure I don't do that\n");
}
?>
    </body>
</html>