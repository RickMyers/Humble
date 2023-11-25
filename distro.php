<?php
/**
        ____  _      __       _ __          __  _           
       / __ \(_)____/ /______(_) /_  __  __/ /_(_)___  ____ 
      / / / / / ___/ __/ ___/ / __ \/ / / / __/ / __ \/ __ \
     / /_/ / (__  ) /_/ /  / / /_/ / /_/ / /_/ / /_/ / / / /
    /_____/______/\__/_/  /_/_.___/\__,_/\__/_______/_/ /_/ 
          / ___/__  ______  ____  ____  _____/ /_           
          \__ \/ / / / __ \/ __ \/ __ \/ ___/ __/           
         ___/ / /_/ / /_/ / /_/ / /_/ / /  / /_             
        /____/\__,_/ .___/ .___/\____/_/   \__/             
             _____/_/   /_/  _       __                     
            / ___/__________(_)___  / /_                    
            \__ \/ ___/ ___/ / __ \/ __/                    
           ___/ / /__/ /  / / /_/ / /_                      
          /____/\___/_/  /_/ .___/\__/                      
                          /_/             
 
    Facilitates the download and installation of the initial repository
 */

    //-------------------------------------------------------------------------------------
    function processVhost($template='app/install/vhost_template.conf',$args=[]) {
        $name       = $args['name'] ?? ($args['project_url'] ?? '' );
        $parts      = explode(':',$name);
        if (count($parts)===3) {
            unset($parts[2]); //get rid of of the port number
            $name = $parts[0].':'.$parts[1];
        }
        //$port       = $parts[2] ?? '80';
        $port       = '80';         //inside the container it will likely always be 80
        $path       = $args['destination_folder']  ?? '';
        $parts      = explode(DIRECTORY_SEPARATOR,$path);
        $root       = array_pop($parts);
        $basedir    = implode(DIRECTORY_SEPARATOR,$parts);
        $ns         = $args['namespace'] ?? '';
        $error_log  = $args['error_log'] ?? '';
        return str_replace(['&&NAME&&','&&PORT&&','&&PATH&&','&&LOG&&','&&BASEDIR&&','&&NAMESPACE&&'],[$name,$port,$path,$error_log,$basedir,$ns],file_get_contents($template));
    }
    //-------------------------------------------------------------------------------------
    function recurseDirectory($path=null) {
        $files = [];
        if ($path !== null) {
            $dir = dir($path);
            while (($entry = $dir->read())!==false ) {
                if (($entry == '.') || ($entry == '..') || ($entry == '.git')) {
                    continue;
                }
                if (is_dir($path.'/'.$entry)) {
                    $files = array_merge($files,recurseDirectory($path.'/'.$entry));
                } else {
                    $files[] = $path.'/'.$entry;
                }
            }
        }
        return $files;
    }
    //main -####################################################################
    $action     = isset($_GET['action']) ? strtolower($_GET['action']) : false;
    switch ($action) {
        case    "fetch" :
            $xml        = simplexml_load_file('application.xml');
            $source     = "../packages/Humble-Distro-".(string)$xml->version->framework.".zip";
            if (!file_exists($source)) {
                header("Content-Type: application/json");
                print('{ "error": "Missing Distro For Version '.(string)$xml->version->framework.'"');
                die();
            }
            $finfo      = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType   = finfo_file($finfo, $source);
            $size       = filesize($source);
            $name       = basename($source);

            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                // cache settings for IE6 on HTTPS
                header('Cache-Control: max-age=120');
                header('Pragma: public');
            } else {
                header('Cache-Control: private, max-age=120, must-revalidate');
                header("Pragma: no-cache");
            }

            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // long ago
            header("Content-Type: $mimeType");
            header('Content-Disposition: attachment; filename="' . $name . '";');
            header("Accept-Ranges: bytes");
            header('Content-Length: ' . filesize($source));
            print readfile($source);
            break;
        case    "serialnumber" :
        case    "serial_number":
        case    "serialNumber" :
            chdir('app');
            require_once "Humble.php";
            $serial_number = 'Error';
            if ($project_attributes = json_decode(urldecode($_REQUEST['project'] ?? ''),true)) {
                $serial_number = Humble::getModel('account/registration')->setProjectDetails(urldecode($_REQUEST['project']))->registerNew($project_attributes);
            }
            print('{ "serial_number": "'.$serial_number.'" }');
            chdir('..');
            break;
        case    "install":
            chdir('app');
            require_once "Humble.php";            
            print(Humble::getModel('account/registration')->install($_REQUEST['serial_number']));
            chdir('..');
            //retrieve somebody's .project file and send it back
            break;
        case    "register":
            chdir('app');
            require_once "Humble.php";
            $result = Humble::getModel('account/registration')->setProjectDetails(json_encode($_REQUEST))->registerExisting();
            print('{ "results": "'.$result.'" }');
            chdir('..');
            break;
        case    "version" :
            header("Content-Type: application/json");
            $xml        = simplexml_load_file('application.xml');
            print('{ "version": "'.(string)$xml->version->framework.'" }');
            break;
        case    "verify" :
            ?>
            <html>
                <head>
                    <link rel="stylesheet" type='text/css' href="/css/admintheme" />
                    <link rel="stylesheet" type='text/css' href="/css/bootstrap" />
                    <style type='text/css'>
                        div { font-family: monospace;}
                        a { font-size: 2em; font-weight: bold; }
                    </style>
                    <script type="text/javascript" src='/js/jquery'></script>
                    <script type="text/javascript" src='/js/bootstrap'></script>
                </head>
                <body>
            <?php
            $files      = recurseDirectory('.');
            $srch       = ['/[A-Z]/','/[a-z]/','/[0-9]/'];
            $repl       = ['-','-','-'];
            $saveroot   = '';
            $savemask   = '';
            $ctr        = 0;
            foreach ($files as $file) {
                $file = substr($file,2); //drop first './'
                $filename = substr($file,strrpos($file,'/'));
                $root     = substr($file,0,strrpos($file,'/'));
                if ($root !== $saveroot) {
                    if ($ctr) {
                        ?></ul></div><?php
                    }
                    $ctr++;
                    $savemask = ' '.preg_replace($srch,$repl,$root);
                    $saveroot = $root;
                    ?>
                       <div><a href='#' onclick='$("#directory_<?=$ctr?>").slideToggle(); return false;'>+</a> <?=$file?></div>
                       <div id='directory_<?=$ctr?>'><ul>
                    <?php
                } else {
                    ?>
                           <div><?=$savemask?><?=$filename?></div>
                    <?php
                }

            }
            ?>
                </body>
            </html>
            <?php
            break;
        case "vhost":
            if ($_REQUEST['project_name'] ?? false) {
                $name      = str_replace(['http://','https://'],['',''],($_REQUEST['name'] && $_REQUEST['name'] ? $_REQUEST['name'] : ($_REQUEST['project_url'] ?? 'localhost')));
                $error_log = isset($_REQUEST['error_log'])&& $_REQUEST['error_log'] ? 'ErrorLog '.$_REQUEST['error_log']:'';
                $port      = $_REQUEST['port'] ?? '80';
                $dir       = $_REQUEST['destination_folder'] ?? ($_REQUEST['current_dir'] ?? '');
                $vhost     = processVhost('app/install/vhost_template.conf',array_merge($_REQUEST,['name'=>$name,'port'=>$port,'destination_folder'=>$dir,'error_log'=>$error_log]));
                print($vhost);
            } else {
                print('{ "error": "Project data not passed in request" }');
            }
            break;
        case "container":
        case "docker" :
        case "config" :
            $ns     = $_REQUEST['namespace'] ?? 'namespace';
            $dir    = str_replace('\\','/',($_REQUEST['destination_folder'] ?? ''));
            $parts  = explode('/',$dir);
            $base   = '';
            for ($i=0; $i<(count($parts)-1); $i++) {
                $base.= ($base) ? '/'.$parts[$i]: $parts[$i];
            }

            $template = str_replace(['&&NAMESPACE&&','&&DIR&&','&&BASEDIR&&'],[$ns,$dir,$base.'/'],file_get_contents('app/install/Docker/dc_template.txt'));   
            $name     = str_replace(['http://','https://'],['',''],(isset($_REQUEST['name']) && $_REQUEST['name'] ? $_REQUEST['name'] : ($_REQUEST['project_url'] ?? 'localhost')));            
            $zip      = new ZipArchive();
            if ($zip->open('temp.zip',ZipArchive::CREATE)) {
                $parts = explode(':',$_REQUEST['project_url']??'');                
                $zip->addFromString('vhost.conf',processVhost('app/install/vhost_template.conf',$_REQUEST));
                $zip->addFromString('DockerFile',str_replace(['&&NAMESPACE&&','&&DIR&&','&&BASEDIR&&','&&NAME&&'],[$ns,$dir,$base,substr($parts[1] ?? '//localhost',2)],file_get_contents('app/install/Docker/Container/container_template.txt')));
                $zip->addFromString('docker-compose.yaml',$template);
                $zip->addFromString('docker_instructions.txt',file_get_contents('app/install/Docker/Container/docker_instructions.txt'));
                $zip->close();
                print(file_get_contents('temp.zip'));
                @unlink('temp.zip');
            } else {
                print("Error creating zip file");
            }
            //print_r($_REQUEST);
            /*
             * Here i will be taking in the project file and returning a zip that will contain the 
             * docker image definition file, with values substituted from the humble.project file
             * as well as server configurations taking into consideration if this is a windows
             * or linux box

             */
            break;
        default :
            header("Content-Type: application/json");
            print('{ "error": "Unsupported Action: '.$action.'" }');
            break;
    }
?>