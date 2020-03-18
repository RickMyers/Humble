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
            //lots of work to be done here...
            //take in the name of the project and the IP
            //return a SN based on the IP and project name... they should get the same SN back for matching IP and project name
            chdir('app');
            require "Humble.php";
            header("Content-Type: application/json");
            $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $num    = '';
            for ($i=0; $i<4; $i++) {
                $num = $num ? $num.'-' : $num;
                for ($j=0; $j<4; $j++) {
                    $num.=substr($chars,rand(0,strlen($chars)-1),1);
                }
            }
            // I probably should record the serial number somewhere in a DB and tie to the request IP
            print('{ "serial_number": "'.$num.'" }');
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
        default :
            break;
    }
?>