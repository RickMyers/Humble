<?php
/**
 *     __  __                __    __        ________    ____
 *    / / / /_  ______ ___  / /_  / /__     / ____/ /   /  _/
 *   / /_/ / / / / __ `__ \/ __ \/ / _ \   / /   / /    / /  
 *  / __  / /_/ / / / / / / /_/ / /  __/  / /___/ /____/ /   
 * /_/ /_/\__,_/_/ /_/ /_/_.___/_/\___/   \____/_____/___/   
 *                                                         
 * Common methods go here
 * 
 */
class CLI 
{
    
    private static $args = [];
    
    /**
     * Converts variable_name to VariableName
     * 
     * @param type $string
     * @param type $first_char_caps
     * @return type
     */
    protected static function underscoreToCamelCase( $string, $first_char_caps = false) {
        return preg_replace_callback('/_([a-z])/', function ($c) { return strtoupper($c[1]); }, (($first_char_caps === true) ? ucfirst($string) : $string));
    }
    
    /**
     * Returns files and paths under a directory
     * 
     * @param type $path
     * @return string
     */
    protected static function recurseDirectory($path) {
        $entries = [];
        if ($path) {
            if (!is_dir($path)) {
                print("What is up with this: ".$path."\n");
            }
            $dir = dir($path);
            while (($entry = $dir->read()) !== false ) {
                if (($entry == '.') || ($entry == '..')) {
                    continue;
                }
                if (is_dir($path.'/'.$entry)) {
                    $entries = array_merge($entries,self::recurseDirectory($path.'/'.$entry));
                } else {
                    $entries[] = $path.'/'.$entry;
                }
            }
        }
        return $entries;
    }    
    
    /**
     * Sorts out what namespaces to use when namespace variable might contain the wildcard '*'
     * 
     * @param array $args
     * @return array
     */
    protected function namespaces($args=[]) {
        $namespaces = [];
        if (isset($args['namespace'])) {
            if ($args['namespace'] == '*') {
                foreach (Humble::packages() as $package) {
                    foreach (Humble::modules($package) as $module) {
                        $namespaces[] = $module;
                    }
                }
                
            }
        }
        return $namespaces;
    }
    
    /**
     * Returns what files actually are found by comparing the manifest to the file structure
     * 
     * @return type
     */
    public static function getManifestContent() {
        $content = [
            'manifest' => [],
            'files' => [],
            'exclude' => [],
            'xref' => []
        ];
        if (file_exists('Humble.manifest')) {
            $content['manifest'] = explode("\n",file_get_contents('Humble.manifest'));
        } else {
            die('Manifest file not found');
        }
        chdir('..');
        foreach ($content['manifest'] as $file) {
            if (substr($file,0,1) == '#') {
                continue;
            }
            if (substr($file,0,1) == '^') {
                $content['exclude'][trim(substr($file,1))] = $file;
                continue;
            }
            $file            = trim($file);
            $parts           = explode(' ',$file);
            $content['xref'][$parts[0]] = (isset($parts[1]) ? $parts[1] : $parts[0]);
            if (substr($file,strlen($file)-1,1)=='*') {
                $content['files'] = array_merge($content['files'],self::recurseDirectory(substr($file,0,strlen($file)-2)));
            } else {
                $content['files'][] = $file;
            }
        }
        chdir('app');
        return $content;
    }
    
    /**
     * Randomly adds some spaces to the end of a word to help with the justify process
     * 
     * @param string $text
     * @param int $width
     * @return string
     */
    private static function expandLine($text,$width) {
        $words = explode(' ',$text);
        for ($i=0; $i<($width - strlen($text)); $i++) {
            $words[rand(0,count($words)-2)] .=' ';                              //don't want to pad last word
        }
        return implode(' ',$words);
    }
    
    /**
     * Justifies an arbitrary piece of text
     * 
     * @param string $block
     * @param int $width
     * @return string
     */
    public static function justify($block='',$width=80) {
        $justified  = [];
        $text       = trim(str_replace(["\r","\n","\t"],['','',''],$block)); 
        $ctr        = 25;                                                       //just in case the dish runs away with the spoon... maximum 25 "lines" or iterations
        while ($text && $width && $ctr--) {
            if (($pos   = strrpos(trim(substr($text,0,$width)),' ')) && (strlen($text) > $width)) {
                $justified[] = "\t".self::expandLine(substr($text,0,$pos),$width);
                $text = substr($text,$pos+1);
            } else {
                $justified[] = "\t".$text;
                $text=false;
            }
        }
        return ($justified ? "\n".implode("\n",$justified)."\n" : '');
    }
    
    /**
     * Formats the detailed help section for each command based on the YAML contents
     * 
     * @param string $command
     * @param array $details
     */
    public static function describe($command=false,$details=[]) {
        $usage = $details['usage'][(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'windows' : 'linux'];
        $command = str_replace('|',' or ',$command);
        $p = ['required'=>'','optional'=>''];
        foreach (['required','optional'] as $section) {
            foreach ($details['parameters'][$section]??[] as $parm => $message) {
                $p[$section] .= "\t\t".str_replace('|',' or ',$parm).' - '.$message."\n";
            }
        }
        $extended = self::justify($details['extended'] ?? '');

        $output = <<<HELP
        
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
                
        Command: --{$command}    {$details['description']}
{$extended}
        Required Parameters:
{$p['required']}
        
        Optional Parameters:
{$p['optional']}
        
        Usage: {$usage}
        
HELP;
        print($output."\n\n");
    }

    /**
     * Removes stuff
     * 
     * @param string $str
     * @return string
     */
    public static function scrub($str) {
        $srch = ["\n","\r","\t"];
        $repl = ["","",""];
        return str_replace($srch,$repl,$str);
    }
    /**
     * Set or return the arguments passed in on the command line
     * 
     * @param array $args
     * @return array
     */
    public static function arguments($args=false) {
        if ($args) {
            self::$args = $args;
        } else {
            return self::$args;
        }
    }
    
    /**
     * Convert an indexed array into an associate [name=value] one
     * 
     * @param array $args
     * @return array
     */
    protected static function processArguments($args=[]) {
        $parms = [];
        foreach ($args as $arg) {
            $parts = explode('=',$arg);
            $parms[$parts[0]] = $parts[1] ?? '';
        }
        return $parms;
    }

    protected static function verifyValues($args,$values) {
        foreach ($values as $parms => $vals) {
            $parm  = false;
            $parts = explode('|',$parms);
            foreach ($parts as $idx => $arg) {
                if (isset($args[$arg])) {
                    $options = [];
                    foreach (explode(',',$vals) as $idx2 => $option) {
                        $options[$option] = true;
                    } 
                    if (!isset($options[$args[$arg]])) {
                        print("\n".'Parameter value incorrect, '.$args[$arg]. ' is not a valid value for \''.$arg."'.\n\n");
                        die('Valid values are: '.$vals."\n\n");
                    }
                }
            }
        }
    }            

    public static function verifyParameters($args,$options) {
        $valid = [];
        foreach (['required','optional'] as $section) {
            foreach (($options['parameters'][$section] ?? []) as $parm => $error_message) {
                $parts            = explode('|',$parm);
                foreach ($parts as $part) {
                    $valid[$part] = true;
                }
            }
        }
        foreach ($args as $arg => $val) {
            if (!isset($valid[$arg])) {
                die("\n'".$arg."' is not a valid parameter, please check the help\n\n");
            }
        }
    }
    /**
     * Verify required parameters are present and organize the arguments in name=value way instead of as an indexed array
     * 
     * @param array $args
     * @param array $options
     * @return array
     */
    public static function verifyArguments($args=false,$options=false) {
        $valid = [];
        if (($args = self::processArguments($args)) && $options) {
            foreach (['required','optional'] as $section) {
                foreach (($options['parameters'][$section] ?? []) as $parm => $error_message) {
                    $parts            = explode('|',$parm);
                    $valid[$parts[0]] = '';
                    foreach ($parts as $part) {
                        if ($valid[$parts[0]] =  $args[$part] ?? false)  {
                            break;
                        }
                    }
                    ($section==='required') ? ($valid[$parts[0]] ? "" : die("\n[missing argument: ".str_replace('|',' or ',$parm).'] '.$error_message."\n") ) : "";
                }
            }
            self::verifyParameters($args,$options);
            if (isset($options['parameters']['values'])) {
                self::verifyValues($args,$options['parameters']['values']);
            }
        }
        return $valid;
    }
    
    /**
     * Returns the application XML file as a parsed object
     * 
     * @return object
     */
    public static function getApplicationXML() {
        return Environment::applicationXML();
    }
    
    /**
     * Just prints the module information retrieved from the database
     * 
     * @param array $mod
     */
    public static function printModule($mod=[]) {
        print("\n\n");
        foreach ($mod as $idx => $val) {
            print("#".$idx."\t = ".$val.";\n");
        }
    }
}

