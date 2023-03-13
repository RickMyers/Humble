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
     * Justifies and arbitrary piece of text
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
        $p = ['required'=>'','optional'=>''];
        foreach (['required','optional'] as $section) {
            foreach ($details['parameters'][$section]??[] as $parm => $message) {
                $p[$section] .= "\t\t".str_replace('|',' or ',$parm).' - '.$message."\n";
            }
        }
        $extended = self::justify($details['extended'] ?? '');

        $output = <<<HELP
                
        Command: --{$command}    {$details['description']}
{$extended}
        Required Parameters:
{$p['required']}
        
        Optional Parameters:
{$p['optional']}
        
        Usage: {$usage}
        
HELP;
        print($output);
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
     * Returns the application XML file as a parsed object
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
    public static function printModule($mod=[]) {
        print("\n\n");
        foreach ($mod as $idx => $val) {
            print("#".$idx."\t = ".$val.";\n");
        }
    }
}

