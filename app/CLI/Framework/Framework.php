<?php
require_once 'CLI/CLI.php';
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
    
    /**
     * 
     */
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
    
    /**
     * 
     * @param type $args
     */
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

    /**
     * When taking a snapshot of the database schema, the increment gets output with the SQL... we need to remove that
     */
    public static function clean() {
        $args = self::arguments();
        $ns    = $args['namespace'];
        $namespaces = ($ns=='*') ? [] : [$ns];
        foreach ($namespaces as $namespace) {
            if ($module = \Humble::module($namespace)) {
                $dh = dir($dir = 'Code/'.$module['package'].'/'.$module['schema_install']);
                if ($dh) {
                    while ($entry = $dh->read()) {
                        if (($entry == '.') || ($entry == '..')) {
                            continue;
                        }
                        $file = $dir.'/'.$entry; $lines = [];
                        print('Cleaning '.$file."\n"); 
                        foreach ($words = explode(' ',file_get_contents($file)) as $word) {
                            if (substr(strtoupper($word),0,15)!=='AUTO_INCREMENT=') {
                                $lines[] = $word;
                            }
                        }
                        file_put_contents($file,implode(' ',$lines));
                    }
                }
            } else {
                print("\n".'Module specified by namespace ['.$ns.'] disabled or does not exist'."\n");
            }
        }
    }    
    
    /**
     * Just prints the version
     */
    public static function version() {
        $xml    = Environment::applicationXML();
        print("\n\n".$xml->version->framework."\n\n");
    }
    
    /**
     * Will update the API Policy document
     */
    public static function apiPolicy() {
        $message = "API Policy File Not Found";
        $project = Environment::project();
        if (file_exists($file   = 'Code/'.$project->package.'/'.$project->module.'/etc/api_policy.json')) {
            $current_policy     = json_decode(file_get_contents($file),true);
            $comments           = $current_policy['comments'] ?? 'Read more about the API policy at https://humbleprogramming.com/pages/APIPolicy.htmls';
            $defaults           = (isset($current_policy['default'])) ? $current_policy['default'] : ['authenticated'=>['read'=>false,'write'=>false]] ;
            $entities           = [];
            foreach (\Humble::entity('humble/modules')->setEnabled('Y')->fetch() as $module) {
                $namespace = $module['namespace'];
                $entities[$namespace] = [];
                foreach (\Humble::entity('humble/entities')->setNamespace($module['namespace'])->fetch() as $entity) {
                    $entities[$namespace][$entity['entity']] = isset($current_policy[$namespace][$entity['entity']]) ? $current_policy[$namespace][$entity['entity']] : ['authenticated'=>['read'=>false,'write'=>false],'public'=>['read'=>false,'write'=>false],'pagination'=>["rows"=>"rows","page"=>"page","cursor"=>"cursor"]];
                }
            }
            $policy = ['comments' => $comments,'default' => $defaults,'entities' => $entities];
            file_put_contents($file,json_encode($policy,JSON_PRETTY_PRINT));
            $message = "API Policy has been updated at ".$file."\n";
        } 
        print("\n".$message."\n");
    }
    
    /**
     * Does a CRC (MD5) check of certain elements of the framework against a master list
     * 
     * @return boolean
     */
    public static function validate() {
        $errors     = [];
        if ($remote = \Environment::project('framework_url')) {
            if ($list = file_get_contents($remote.'/distro/validation')) {
                chdir('..');
                foreach (simplexml_load_string($list) as $target => $opts) {
                    $attrs = $opts->attributes();
                    $path  = isset($attrs['path']) ? $attrs['path'] : false;
                    $crc   = (string)$opts;
                    if ($target && $path && $crc) {
                        if (!($crc === md5(file_get_contents($path)))) {
                            $errors[] = 'Error: '.ucfirst($target).' At '.$path.' Failed To Validate';
                        }
                    }
                }
                chdir('app');
            } else {
                $errors[] = 'Error: Unable to obtain the validation list';
            }
        } else {
            $errors[] = 'Error: Unable to identify the validation list source';
        }
        if ($ecnt = count($errors)) {
            foreach ($errors as $error) {
                print($error."\n");
            }
        }
        return ($ecnt === 0);
    }
    
    public static function manual($args) {
        $tag    = $args['tag'];
        $attr   = $args['attr'] ?? false;
        //TODO!
    }
}



