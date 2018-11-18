<?php
    ob_start();
?>
<html>
    <head>
        <link rel='stylesheet' type='text/css' href='/web/css/index.css' />
        <style type="text/css">
            /* url(/images/paradigm/bg_graph.png)*/
            body {
                height: 100%; box-sizing: border-box;  background-size: cover;
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
                width: 705px; padding: 20px 30px; border: 1px solid #aaf; background-color: #e0e0e0;
            }
        </style>
        <script type="text/javascript" src="/web/js/jquery.js"></script>
        <script type="text/javascript" src="/web/js/EasyAjax.js"></script>
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
            die("<table width='100%' height='100%'><tr><td align='center'><h1 style='color: white'>Please enable the application before attempting to install</h1></td></tr></table>");
        }
        if (isset($xml->status->installer) && ((int)$xml->status->installer)) {
            //nop; everything is good
        } else {
            die("<table width='100%' height='100%'><tr><td align='center'><h1 style='color: white'>Executing the installation script is currently disabled</h1></td></tr></table>");
        }
    } else {
        die("The application is not correctly configured.  Correct the application configuration file and try again");
    }
} else {
    die("There is an error in the application configuration file");
}
$method = (isset($_POST['method'])) ? $_POST['method'] : "INIT";
switch ($method) {
    case "INIT"         :
        ?>
            <table id="installer-area" style="width: 100%; height: 100%" cellspacing='0' cellpadding='0'>
                <tr style="height: 20px">
                    <td>
                        <div class="flat-brick" style="background-color: #0A2327"></div>
                        <div class="flat-brick" style="background-color: #0F3F3F"></div>
                        <div class="flat-brick" style="background-color: #818D07"></div>
                        <div class="flat-brick" style="background-color: #D39423"></div>
                        <div class="flat-brick" style="background-color: #E74723"></div>
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
                        <div class='installer-form-div' id="installer-form-div" style='text-align: left; position: relative; display: block;'>
                            <div style="padding: 10px; color: white; font-size: 1em; font-family: sans-serif; margin-bottom: 20px; text-align: center; background-color: #0033CC">
                                Welcome to the Installation for <?=$xml->name?>
                            </div>
                            <form name='installer-form' method='post' id='installer-form' onsubmit="" action="">
                                <input type="hidden" name="method" id="method" value="INSTALL" />

                                <fieldset style="float: left; width: 250px; position: relative" id="div_1"><legend>Administrator Information</legend>
                                    <input type='text' class='installer-form-field' id='email' name='email' />
                                    <div class='installer-field-description'>E-Mail</div>

                                    <input type='text' class='installer-form-field' id='username' name='username' />
                                    <div class='installer-field-description'>User Login Id</div>

                                    <input type='password' class='installer-form-field' id='pwd' name='pwd' />
                                    <div class='installer-field-description'>Password</div>

                                    <input type='password' class='installer-form-field' id='confirm' name='confirm' />
                                    <div class='installer-field-description'>Confirm Password</div>

                                    <input type='text' class='installer-form-field' id='firstname' name='firstname' />
                                    <div class='installer-field-description'>First Name</div>

                                    <input type='text' class='installer-form-field' id='lastname' name='lastname' />
                                    <div class='installer-field-description'>Last Name</div>
                                    <input type="button" id="install-submit" name="install-submit" value=" Install " />
                                </fieldset>

                                <fieldset style="display: inline-block; width: 350px; position: relative" id="div_2"><legend>Database Information</legend>
                                <input type='text' placeholder="127.0.0.1:3306" class='installer-form-field' id='dbhost' name='dbhost' /><input type='button' value=' Test ' id='install-test' name='install-test' />
                                <div class='installer-field-description'>MySQL Host (localhost:port or leave out port for default)</div>

                                <input type='text' class='installer-form-field' id='db' name='db' />
                                <div class='installer-field-description'>MySQL Database Name</div>

                                <input type='text' class='installer-form-field' id='userid' name='userid' />
                                <div class='installer-field-description'>MySQL User Id</div>

                                <input type='password' class='installer-form-field' id='password' name='password' />
                                <div class='installer-field-description'>MySQL Password</div>

                                <input type='text' placeholder="127.0.0.1:27017" class='installer-form-field' id='mongo' name='mongo' /><input type="button" value=" Test " id='mongo-test' name='mongo-test' />
                                <div class='installer-field-description'>MongoDB Host</div>

                                <input type='text' class='installer-form-field' id='mongo_userid' name='mongo_userid' />
                                <div class='installer-field-description'>MongoDB User Id</div>

                                <input type='text' class='installer-form-field' id='mongo_password' name='mongo_password' />
                                <div class='installer-field-description'>MongoDB Password</div>

                                <input type='text' placeholder="127.0.0.1:11211" class='installer-form-field' id='cache' name='cache' />
                                <div class='installer-field-description'>Cache Server </div>
                                </fieldset>
                                <div style="clear: both"></div>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr style="height: 20px">
                    <td>
                        <div class="flat-brick" style="background-color: #0A2327"></div>
                        <div class="flat-brick" style="background-color: #0F3F3F"></div>
                        <div class="flat-brick" style="background-color: #818D07"></div>
                        <div class="flat-brick" style="background-color: #D39423"></div>
                        <div class="flat-brick" style="background-color: #E74723"></div>
                        <div style="clear: both"></div>
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
                new EasyEdits('/web/edits/install.json','install-form');
                $('#div_1').height($('#div_2').height());
            </script>
        <?php
        break;
    case "INSTALL"      :
        ob_start();
        $step    = 0;
        file_put_contents('install_status.json','{ "stage": "Preparing",  "step": "Initializing...", "percent": 0 }');
        $host   = isset($_POST['dbhost'])           ? $_POST['dbhost']    : false;
        $uid    = isset($_POST['userid'])           ? $_POST['userid']    : false;
        $pwd    = isset($_POST['password'])         ? $_POST['password']  : false;
        $mongo  = isset($_POST['mongo'])            ? $_POST['mongo']     : false;
        $mongou = isset($_POST['mongo_userid'])     ? $_POST['mongo_userid']     : false;
        $mongop = isset($_POST['mongo_password'])   ? $_POST['mongo_password']     : false;
        $db     = isset($_POST['db'])               ? $_POST['db']        : false;
        $cache  = isset($_POST['cache'])            ? $_POST['cache']     : false;
        $srch   = array('&&USERID&&','&&PASSWORD&&','&&DATABASE&&','&&HOST&&','&&MONGO&&','&&CACHE&&','&&MONGOUSER&&','&&MONGOPWD&&');
        $repl   = array($uid,$pwd,$db,$host,$mongo,$cache,$mongou,$mongop);
        if (!file_exists('Humble.project')) {
            die('<h1>Missing Project File.  Run "humble --project" at the command line to create one</h1>');
        }
        $project = json_decode(file_get_contents('Humble.project'));
        @mkdir('../Settings/'.$project->namespace,0775,true);
        @mkdir('images',0775,true);
        file_put_contents("../Settings/".$project->namespace."/Settings.php",str_replace($srch,$repl,file_get_contents('app/Code/Base/Humble/lib/sample/install/Settings.php')));
        chdir('app');
        require_once('Humble.php');
        $util    = \Environment::getInstaller();
        $modules = \Environment::getRequiredModuleConfigurations();
        $percent = (count($modules)*2)+4;
        file_put_contents('../install_status.json','{ "stage": "Starting", "step": "Building Application Module", "percent": '.(++$step*$percent).' }');
        $cmd = 'php Module.php --b namespace='.$project->namespace.' package='.$project->package.' module='.$project->module.' prefix='.$project->namespace.'_';
        exec($cmd);
        foreach ($modules as $idx => $etc) {
            file_put_contents('../install_status.json','{ "stage": "Installing", "step": "Installing '.$etc.'", "percent": '.(++$step*$percent).' }');
            print('###########################################'."\n");
            print('Installing '.$etc."\n");
            print('###########################################'."\n\n");
            $util->install($etc);
        }
        $util = \Environment::getUpdater();
        foreach ($modules as $idx => $etc) {
            file_put_contents('../install_status.json','{ "stage": "Updating", "step": "Updating '.$etc.'", "percent": '.(++$step*$percent).' }');
            print('###########################################'."\n");
            print('Updating '.$etc."\n");
            print('###########################################'."\n\n");
            $util->update($etc);
        }
        //
        // ###NOW RUN UPDATE ON EACH MODULE!!!!#######
        //
        file_put_contents('../install_status.json','{ "stage": "Finalizing", "step": "Registering Administrator", "percent": '.(++$step*$percent).' }');
        $landing_page = (string)str_replace("\\","",$project->landing_page);
        $landing = explode('/',$landing_page);
        $ins     = Humble::getModel('humble/utility');
        file_put_contents('../install_status.json','{ "stage": "Finalizing", "step": "Activiting Application Module", "percent": '.(++$step*$percent).' }');
        shell_exec("php Module.php --i ".$project->namespace." Code/".$project->package."/".$project->module."/etc/config.xml");
        shell_exec("php Module.php --e ".$project->namespace);
        $util->disable();                                                       //Prevent accidental re-run
        ob_start();
        $uid    = \Humble::getEntity('humble/users')->setEmail($_POST['email'])->setUserName($_POST['username'])->setPassword(MD5($_POST['pwd']))->save();
        $results = ob_get_flush();
        if (!$uid) {
            file_put_contents('oops.txt',$results);
        }
        \Humble::getEntity('humble/user_identification')->setId($uid)->setFirstName($_POST['firstname'])->setLastName($_POST['lastname'])->save();
        \Humble::getEntity('humble/user_permissions')->setId($uid)->setAdmin('Y')->setSuperUser('Y')->save();
        $ins->setUid($uid)->setNamespace($project->namespace)->setEngine('Smarty3')->setName($landing[2])->setAction($landing[3])->setDescription('Basic Controller')->setActionDescription('The Home Page')->createController(true);
        if (!$cache) {

        }
        session_start();
        $_SESSION['uid'] = $uid;
        copy('install/driver.bat',strtolower((string)$project->factory_name).'.bat');
        copy('install/humble.sh',strtolower((string)$project->factory_name).'.sh');
        file_put_contents('../install_status.json','{ "stage": "Complete", "step": "Finished", "percent": 100 }');
        file_put_contents('../install.log',ob_get_flush());
        break;
    default             :
        die("I'm not sure what you want, but I'm pretty sure I don't do that");
}
?>
    </body>
</html>