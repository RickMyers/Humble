<?php
/**
        __  __                __    __   
       / / / /_  ______ ___  / /_  / /__ 
      / /_/ / / / / __ `__ \/ __ \/ / _ \
     / __  / /_/ / / / / / / /_/ / /  __/
    /_/ /_/\__,_/_/ /_/ /_/_.___/_/\___/ 
           ______           __           
          / ____/___ ______/ /_  ___     
         / /   / __ `/ ___/ __ \/ _ \    
        / /___/ /_/ / /__/ / / /  __/    
        \____/\__,_/\___/_/ /_/\___/     
                                     
 * Managed by the main Humble factory, do not call this yourself 
 */
class Cache {
        
        private static $cacher = null;
        /**
         *
         */
        public function __construct() {
            //empty
        }

        /**
         * Necessary for basic level of debugging except for entities.  Do not over ride this method if you are in an entity
         *
         * @return system
         */
        public static function getClassName() {
            return __CLASS__;
        }
        
        public static function connect() {
            
        }
        
        public static function get() {
            
        }
        
        public static function set() {
            
        }
}
