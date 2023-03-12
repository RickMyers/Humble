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
    
    public static function describe($command=false,$details=[]) {
        $usage = $details['usage'][(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'windows' : 'linux'];
        $p = ['required'=>'','optional'=>''];
        foreach (['required','optional'] as $section) {
            foreach ($details['parameters'][$section]??[] as $parm => $message) {
                $p[$section] .= "\t\t".str_replace('|',' or ',$parm).' - '.$message."\n";
            }
        }
        $output = <<<HELP
                
        Command: --{$command}    {$details['description']}
        
        Required Parameters:
{$p['required']}
        
        Optional Parameters:
{$p['optional']}
        
        Usage: {$usage}
        
HELP;
        print($output);
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
        }
        return $valid;
    }
    
    /**
     * Returns the application xml file as a parsed object
     * 
     * @return object
     */
    public static function getApplicationXML() {
        $data = (file_exists('../application.xml')) ? file_get_contents('../application.xml') : die("Error, application file not found");
        $xml  = simplexml_load_string($data);
        return $xml;
    }
    
    /**
     * Just prints the module information retrieved from the database
     * 
     * @param array $mod
     */
    public static function printModule($mod) {
        print("\n\n");
        foreach ($mod as $idx => $val) {
            print("#".$idx."\t = ".$val.";\n");
        }
    }
}

