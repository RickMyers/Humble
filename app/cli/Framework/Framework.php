<?php
require_once 'cli/CLI.php';
class Framework extends CLI 
{

    /**
     * Checks to see if a namespace is already being used
     */
    public static function namespaceAvailability() {
        $args = self::arguments();
        if ($data = \Humble::entity('humble/modules')->setNamespace($args['namespace'])->load(true)) {
            
            print("That namespace is already in use\n\n");
            print("Information on that module follows:\n");
            self::printModule($data);
        } else {
            print("\nThat namespace ({$args['namespace']}) is available\n\n");
        }

    }
    
    /**
     * Checks to see if a database prefix is already being used
     */
    public static function prefixAvailability() {
        $args = self::arguments();
        if ($data = \Humble::entity('humble/modules')->setPrefix($args['prefix'])->load(true)) {                
            print("That prefix is already in use\n\n");
            print("Information on that module follows:\n");
            self::printModule($data);
        } else {
            print("\nThat ORM prefix ({$args['prefix']}) is available\n\n");
        }
    }
    
    public static function preserve() {
        $args       = self::arguments();
        $directory  = $args['directory'];
        print('Attempting to copy: '.$directory."\n");
        if ($directory) {
            $util       = Humble::helper('humble/directory');
            $directory  = (substr($directory,0,1)==='/') ? substr($directory,1) : $directory;  //convert from absolute to relative path
            if (is_dir('../'.$directory)) {
                @mkdir('../../'.$directory,0775,true);
                $util->copyDirectory('../'.$directory,'../../'.$directory);
            } else {
                print("Directory doesn't exist\n");
            }
        } else {
            print('Directory not found');
        }
    }
    
    public static function restore($args) {
        $args       = self::arguments();
        $directory  = $args['directory'];
        print('Attempting to restore: '.$directory."\n");
        if ($directory) {
            $util       = Humble::helper('humble/directory');
            $directory  = (substr($directory,0,1)==='/') ? substr($directory,1) : $directory;  //convert from absolute to relative path
            if (is_dir('../../'.$directory)) {
                @mkdir('../'.$directory,0775,true);
                $util->copyDirectory('../../'.$directory,'../'.$directory);
            } else {
                print("Directory doesn't exist\n");
            }
        } else {
            print('Directory not found');
        }
    }

    public static function clean() {
        $parms = self::arguments();
        $file  = $args['file'];
        if ($file) {
            if (file_exists($file)) {
                $lines = [];
                foreach (explode("\n",file_get_contents($file)) as $row) {
                    if (($pos = strpos($row,"AUTO_INCREMENT="))!==false) {
                        $pre = substr($row,0,$pos);
                        $post = strpos(substr($row,$pos),' ');
                        $row = $pre.substr(substr($row,$pos),$post);
                    }
                    $lines[] = $row;
                }
                file_put_contents($file,implode("\n",$lines));
            } else {
                print('File specified ['.$file.'] does not exist');
            }
        } else {
            print('Must pass in an argument of file=###');
        }

    }    
    
    /**
     * Just prints the version
     */
    public static function version() {
        $xml    = simplexml_load_string(file_get_contents('../etc/application.xml'));
        print("\n\n".$xml->version->framework."\n\n");
    }
}



