<?php
/*
 * Resource loader for text-based (non-binary) web elements
 *
 * Sample:
 *
 *      /js/common   <- Load all javascript that is in the common package
 *      /js/optional <- Load javascript in the 'optional' package
 *      /css/common  <- Load CSS that is in common package
 *      /edits/desktop/login  <- fetch the login.json edits located in the
 *                               desktop namespace
 */

//---------------------------------------------------------------------------------------

/*
 * Either the secure attribute is not set or the file is public, else if session id is set file is available
 */
function secureCheck($file=[]) {
    return (!isset($file['secure']) || ($file['secure']==='N')) ? true : isset($_SESSION['uid']); 
}
    ob_start();
    chdir('app');                               //always start in this directory
    require_once('Humble.php');                   //our friend
    $orm = \Humble::entity('humble/'.$_GET['type']);
    $orm->setNamespace('');                     //clear the namespace
    $packages = array();

    switch ($_GET['type']) {
        case    'js'        :   header('Content-Type: application/javascript');
                                $orm->setPackage(str_replace('.js','',$_GET['package']));
                                $orm->_orderBy('weight');
                                $packageFiles = $orm->fetchEnabled(str_replace('.js','',$_GET['package']));
                                foreach ($packageFiles as $idx => $file) {
                                    if (secureCheck($file)) {
                                        if (!isset($packages[$file['namespace']])) {
                                            $mod = \Humble::getModule($file['namespace']);
                                            if (!$mod) {
                                                continue;
                                            }
                                            $packages[$file['namespace']] = $mod;
                                        }
                                        if ((substr($file['source'],0,1)=='/')) {
                                            print(file_get_contents($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$file['source']).';');
                                        } else if (substr($file['source'],0,4)=="http") {
                                            print(file_get_contents($file['source']).'; ');
                                        } else {
                                            $file = 'Code/'.$packages[$file['namespace']]['package'].'/'.$file['source'];
                                            if (file_exists($file)) {
                                                print("\n\n// ***************** $file *************\n//\n\n");
                                                print(file_get_contents($file).'; ');
                                            } else {
                                                \Log::console("Javascript file not found: ".$file);
                                            }
                                        }
                                    }
                                }
                                break;
        case    'css'       :   header('Content-Type: text/css');
                                $orm->setPackage(str_replace('.css','',$_GET['package']));
                                $orm->_orderBy('weight');
                                $packageFiles = $orm->fetchEnabled(str_replace('.css','',$_GET['package']));
                                foreach ($packageFiles as $idx => $file) {
                                    if (secureCheck($file)) {
                                        if (!isset($packages[$file['namespace']])) {
                                            $mod = \Humble::getModule($file['namespace']);
                                            if (!$mod) {
                                                continue;
                                            }
                                            $packages[$file['namespace']] = $mod;
                                        }
                                        $file = 'Code/'.$packages[$file['namespace']]['package'].'/'.$file['source'];
                                        if (file_exists($file)) {
                                            print("\n\n/***************** $file *************/\n\n");
                                            print(file_get_contents($file).' ');
                                        } else {
                                            \Log::console("CSS file not found: ".$file);
                                        }
                                    }
                                }
                                break;
        case    'edits'     :   header('Content-Type: application/json');
                                $orm->setNamespace($_GET['n']);
                                $orm->setForm($_GET['f']);
                                $data = $orm->load(true);
                                $module = \Humble::getModule($_GET['n']);
                                if ($module && secureCheck($data)) {
                                    $file = 'Code/'.$module['package'].'/'.$orm->getSource();
                                    if (file_exists($file)) {
                                        print(file_get_contents($file));
                                    } else {
                                         \Log::console("EDIT file not found: ".$file);
                                    }
                                }
                                break;
        case    'pages'     :   header('Content-Type: text/html');
                                $orm->setNamespace($_GET['n']);
                                $orm->setPage($_GET['f']);
                                $data = $orm->load();
                                if ($orm->getSource()) {
                                    $module = \Humble::getModule($_GET['n']);
                                    if ($module && secureCheck($data)) {
                                        $file = 'Code/'.$module['package'].'/'.$orm->getSource();
                                        if (file_exists($file)) {
                                            print(file_get_contents($file));
                                        } else {
                                             \Log::console("'Static Page file not found: ".$file);
                                        }
                                    }
                                } else {
                                    print("The page mapped to ".$_GET['f']." was not found.\n");
                                }
                                break;
        case    'templates'   :   header('Content-Type: text/html');
                                $orm->setNamespace($_GET['n']);
                                $orm->setTemplate($_GET['f']);
                                $data = $orm->load();
                                if ($orm->getSource()) {
                                    $module = \Humble::getModule($_GET['n']);
                                    if ($module && secureCheck($data)) {
                                        $file = 'Code/'.$module['package'].'/'.$orm->getSource();
                                        if (file_exists($file)) {
                                            print(file_get_contents($file));
                                        } else {
                                             \Log::console("Template file not found: ".$file);
                                        }
                                    }
                                } else {
                                    print("The template mapped to ".$_GET['f']." was not found.\n");
                                }
                                break;
        default             :   break;
    }
    //ob_end_flush();
?>