<?php
/*
 * Resource loader for text-based (non-binary) web elements
 *
 * Sample:
 *
 *      /js/common   <- Load all javascript that is in the common package
 *      /js/optional <- Load javascript in the 'optional' package
 *      /css/common  <- Load CSS that is in common package
 *      /mjs/humble/myjs.js <- Return the myjs.js file in the Humble package, web/js folder
 *      /edits/desktop/login  <- fetch the login.json edits located in the desktop namespace
 *                               
 */

//---------------------------------------------------------------------------------------

/*
 * Either the secure attribute is not set or the file is public, else if session id is set file is available
 */
function secureCheck($file=[]) {
    return (!isset($file['secure']) || ($file['secure']==='N')) ? true : isset($_SESSION['uid']); 
}
    ob_start();
    require_once('Humble.php');                   //our friend
    if ($_GET['type']!=='mjs') {
        $orm = \Humble::entity('humble/'.$_GET['type'])->setNamespace('');
    }
    $packages = array();
    $production = \Environment::isProduction();
    switch ($_GET['type']) {
        case 'js':
            header('Content-Type: application/javascript');
            $orm->setPackage(str_replace('.js','',$_GET['package']))->_orderBy('weight');
            $packageFiles = $orm->fetchEnabled(str_replace('.js','',$_GET['package']));
            foreach ($packageFiles as $idx => $file) {
                if (secureCheck($file)) {
                    if (!isset($packages[$file['namespace']])) {
                        $mod = \Humble::module($file['namespace']);
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
                            if (!$production) {
                                print("\n\n// ***************** $file *************\n//\n\n");
                            }
                            print(file_get_contents($file).'; ');
                        } else {
                            if (!$production) {
                                \Log::console("Javascript file not found: ".$file);
                            }
                        }
                    }
                }
            }
            break;
        case 'mjs' :
            header('Content-Type: application/javascript');            
            $ns   = $_GET['namespace'];
            if ($module = Humble::entity('humble/modules')->setNamespace($ns)->load(true)) {
                $file   = $_GET['file'];
                $source = 'Code/'.$module['package'].'/'.$module['module'].'/web/js/'.$file;
                if (file_exists($source)) {
                    print(file_get_contents($source));
                }
            }
            break;
        case 'component' :
            header('Content-Type: application/javascript');            
            $ns   = $_GET['namespace'];
            if ($module = Humble::entity('humble/modules')->setNamespace($ns)->load(true)) {
                $file   = $_GET['file'];
                $source = 'Code/'.$module['package'].'/'.$module['module'].'/web/components/'.$file;
                if (file_exists($source)) {
                    print(file_get_contents($source));
                }
            }
            break;            
        case 'css' :   
            header('Content-Type: text/css');
            $orm->setPackage(str_replace('.css','',$_GET['package']))->_orderBy('weight');
            $packageFiles = $orm->fetchEnabled(str_replace('.css','',$_GET['package']));
            foreach ($packageFiles as $idx => $file) {
                if (secureCheck($file)) {
                    if (!isset($packages[$file['namespace']])) {
                        $mod = \Humble::module($file['namespace']);
                        if (!$mod) {
                            continue;
                        }
                        $packages[$file['namespace']] = $mod;
                    }
                    $file = 'Code/'.$packages[$file['namespace']]['package'].'/'.$file['source'];
                    if (file_exists($file)) {
                        if (!$production) {
                            print("\n\n/***************** $file *************/\n\n");
                        }
                        print(file_get_contents($file).' ');
                    } else {
                        if (!$production) {
                            \Log::console("CSS file not found: ".$file);
                        }
                    }
                }
            }
            break;
        case 'edits' :
            header('Content-Type: application/json');
            $data = $orm->setNamespace($_GET['n'])->setForm($_GET['f'])->load(true);
            $module = \Humble::module($_GET['n']);
            if ($module && secureCheck($data)) {
                $file = 'Code/'.$module['package'].'/'.$orm->getSource();
                if (file_exists($file)) {
                    print(file_get_contents($file));
                } else {
                    if (!$production) {
                        \Log::console("EDIT file not found: ".$file);
                    }
                }
            }
            break;
        default:
            break;
    }
    //ob_end_flush();
?>