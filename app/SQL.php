<?php
/**
    _____ ____    __                      
  / ___// __ \  / /                      
  \__ \/ / / / / /                       
 ___/ / /_/ / / /___                     
/____/\___\_\/_____/ __                  
   / ____/___ ______/ /_____  _______  __
  / /_  / __ `/ ___/ __/ __ \/ ___/ / / /
 / __/ / /_/ / /__/ /_/ /_/ / /  / /_/ / 
/_/    \__,_/\___/\__/\____/_/   \__, /  
                                /____/   
 Used mainly during installation
 */
class SQL {
  
    /**
     * Sets up MySQL Resources
     * 
     * @return array
     */
    protected static function MySQL() {
        $SQL=[];
        return $SQL;
    }
    
    /**
     * Sets up SQLLite Resources
     * 
     * @return array
     */
    protected static function SQLLite() {
        $SQL=[];
        return $SQL;        
    }
    
    /**
     * Sets up SQL Server Resources
     * 
     * @return array
     */
    protected static function SQLSrvr() {
        $SQL=[];
        return $SQL;        
    }
    
    /**
     * Sets up PostGRES Resources
     * 
     * @return array
     */
    protected static function PostGRES() {
        $SQL=[];
        return $SQL;        
    }
    
    /**
     * Returns a specified SQL string resource related to the engine being used
     * 
     * @param type $engine
     * @param type $resource
     * @return type
     */
    public static function fetch($engine='MySQL',$resource=false) {
        $resources = [];
        if ($engine=='SQLSrvr') {
            
        } else if ($engine=='SQLLite') {
            $resources = self::SQLLite();
        } else if ($engine=='PostGRES') {
            $resources = self::PostGRES();
        } else {
            $resources = self::MySQL();
        }
        return (isset($resources[$resource])) ? $resources[$resources] : 'Resource Not Found';
    }
}