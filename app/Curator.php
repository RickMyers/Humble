<?php
/*
    ______                 __            
  / ____/_  ___________ _/ /_____  _____
 / /   / / / / ___/ __ `/ __/ __ \/ ___/
/ /___/ /_/ / /  / /_/ / /_/ /_/ / /    
\____/\__,_/_/   \__,_/\__/\____/_/     
                                        
            I prep stuff
 */
class Curator {

    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     * Necessary for basic level of debugging except for entities.  Do not over ride this method if you are in an entity
     *
     * @return system
     */
    public function className() {
        return __CLASS__;
    }

    /**
     * This routine accepts a resource identifier and then gets the list of files associated to that resource packing them into a single zip file for download
     * 
     * @param type $resource
     * @return string
     */
    public static function prepare($resource=false) : mixed {
        $result = '';
        if ($resources = json_decode(file_get_contents('Code/Framework/Humble/lib/sample/curated/resources.json'),true)) {
            if (isset($resources[$resource])) {
                $zip = new ZipArchive();
                if ($zip->open('tempresource.zip',ZipArchive::CREATE)) {
                    foreach ($resources[$resource] as $file => $source) {
                        $zip->addFromString($file,file_get_contents($source));
                    }
                    $zip->close();
                    $result = file_get_contents('tempresource.zip');
                    @unlink('tempresource.zip');                    
                }
            } else {
                die("Resource ".$resource." was not found on the curated list\n");
            }
        }
        return $result;
    }
}
