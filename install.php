<?php
    ob_start();
    function postUpdate($stage='Preparing',$step='Initializeing',$percent=0) {
        $percent = ($percent > 100) ? 100 : $percent;
        file_put_contents('../install_status.json','{ "stage": "'.$stage.'",  "step": "'.$step.'", "percent": '.$percent.' }');        
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
//Since this is to install this framework, we have to use a different mechanism outside the framework to do this.
//
//IMPORTANT:
//
//   *** To Enable Install, edit the file application.xml file in the main modules /etc/ folder and set the value to enable (1) ***
//
//----------------------------------------------------------------------------------------------------------------

$project    = json_decode(file_get_contents('Humble.project'));
$xml        = simplexml_load_string((file_exists('app/Code/'.$project->package.'/'.$project->module.'/etc/application.xml')) ? file_get_contents('app/Code/'.$project->package.'/'.$project->module.'/etc/application.xml') : die("Install is not possible at this time due to missing application.xml meta data file."));

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
$docker     = file_exists('Docker/docker-compose.yaml');
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
                        <div id="installer-tabs" class='installer-form-tabs' style="height: auto; padding: 0px 30px; border: 1px solid #aaf; border-bottom: 0px;"></div>
                        <form name='installer-form' method='post' id='installer-form' onsubmit="return false" action="" style="margin: 0px; border: 0px; font-family: sans-serif;">
                            <div class='installer-form-div' id="installer-form-div" style='text-align: left; position: relative; display: block;'>
                                <div style="padding: 10px; color: white; font-size: 1em; font-family: sans-serif; margin-bottom: 20px; text-align: center; background-color: #0F3F3F">
                                    Welcome to the Installation for <?=$xml->name?>
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
                            <div id="installer-options" class='installer-form-div' style="display: flex; flex-direction: column; justify-content: center; align-items: center">
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
                                    </select><br /><br />
                                    <label for="landing-default">Landing Page: </label>
                                    <input type="radio" name="landing" id="landing-default" checked="checked" value="default" /> Default
                                    <input type="radio" name="landing" id="landing-enhanced" value="enhanced" /> Enhanced (alpha)<br /><br />
                                    <input type="checkbox" name="authorization_engine" id="authorization_engine" value="Y" /> Include Basic Authorization Engine<br /><br />
                                    <input type="checkbox" name="roles_and_relationships" id="roles_and_relationships" value="Y" /> Include Roles And Relationships Features<br /><br />
                                    <input type="checkbox" name="socket_server" id="socket_server" value="Y" /> Install Socket Server (Node.js/NPM Required)<br /><br />
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
        $fname  = isset($_POST['firstname'])        ? $_POST['firstname']       : '';
        $lname  = isset($_POST['lastname'])         ? $_POST['lastname']        : '';
        $use    = isset($_POST['templater'])        ? $_POST['templater']       : 'Smarty';
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
        file_put_contents("../Settings/".$project->namespace."/Settings.php",str_replace($srch,$repl,file_get_contents('app/Code/Framework/Humble/lib/sample/install/Settings.php')));
        chdir('app');
        postUpdate('Preparing','Initializing',0);
        require_once('Humble.php');
        $util            = \Environment::getInstaller();
        $modules         = \Environment::getRequiredModuleConfigurations();
        $project         = \Environment::getProject();
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
        
        $admin_id  = \Humble::entity('admin/users')->newUser($_POST['username'],MD5($upwd),$fname,$lname,$email);
        
        $install_manager = Humble::model('humble/manager');        
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
        //need to create the user tables... should do that
        $user_id      = \Humble::entity('default/users')->newUser($_POST['username'],MD5($upwd),$fname,$lname,$email);        
        $util->disable();                                                       //Disabling the installer to prevent accidental re-run
        
        $results      = ob_get_flush();
        if (!$user_id) {
            file_put_contents('install_failed.txt',$results);
            print('<pre>'.$results."\n\n\n".'</pre>');
            die('Install did not complete, no user was created'."\n");
        } 
        print('</pre>');
        session_start();
        $_SESSION['uid'] = $user_id;
        print('Attempting to create drivers'."\n");
        $x = (file_exists('humble.bat')) ? rename('humble.bat',strtolower((string)$project->factory_name).'.bat'): '';
        $x = (file_exists('humble.sh'))  ? rename('humble.sh',strtolower((string)$project->factory_name).'.sh') : '';
        $x = (file_exists('../Humble.php')) ? @unlink('../Humble.php') : '';
        if (file_exists('../.htaccess')) {
            $parts  = explode('/',$project->landing_page);
            $srch   = ['&&NAMESPACE&&','&&PACKAGE&&','&&MODULE&&','&&CONTROLLER&&','&&PAGE&&'];
            $repl   = [$project->namespace,$project->package,$project->module,$parts[2],$parts[3]];
            file_put_contents('../.htaccess',str_replace($srch,$repl,file_get_contents('../.htaccess')));
        }
        print("done with creating drivers\n\n");
        $log = ob_get_flush();
        //if error, then print log
        print('<textarea style="width: 100%; height: 100%">'.$log.'</textarea>');
        postUpdate('Complete','Finished',100);
        file_put_contents('../install.log',$log);
        @unlink('../install_status.json');
        ?>
        <script>
            window.location.href = '/index.html?message=Installation Completed, Please Login...';
        </script>
        <?php
        break;
    default             :
        die("I'm not sure what you want, but I'm pretty sure I don't do that\n");
}
?>
    </body>
</html>