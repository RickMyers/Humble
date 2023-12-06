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
        
        private static $cacher      = null;
        private static $cacheFailed = false;
        private static $cacheConn   = null;
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
            global $use_redis;
            if ($use_redis ?? false) {
                
            } else {
                if ($cache_server = Environment::settings()->getCacheHost()) {
                    $server = explode(':',$cache_server);                
                    if (self::$cacher = new Memcache()) {
                        if (!@self::$cacheConn = self::$cache->connect($server[0],($server[1] ?? 11211))) {
                            self::$cacheFailed = true;
                        }
                    }   
                }
            }
            
            return self::$cacher;
            
        }
        
        public static function get() {
            global $use_redis;
        }
        
        public static function set() {
            global $use_redis;
        }
}
