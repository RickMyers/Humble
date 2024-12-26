<?php
/***
       ____    __  __  _
      / __/__ / /_/ /_(_)__  ___ ____
     _\ \/ -_) __/ __/ / _ \/ _ `(_-<
    /___/\__/\__/\__/_/_//_/\_, /___/
                           /___/
	Required per application

*/
require 'vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient; 
use Aws\Exception\AwsException; 

class Settings
{

    private $userid         = NULL;
    private $password       = NULL;
    private $database       = NULL;
    private $dbhost         = NULL;
    private $mongodb        = null;
    private $mongodbUserId  = null;
    private $mongodbPwd     = null;
    private $smtpUserName   = null;
    private $smtpPassword   = null;
    private $smtpHost       = null;
    private $cacheHost      = null;
    private $memcache_ip    = '127.0.0.1';
    private $memcache_port  = '11211';
    protected $secrets_id   = 'HedisRDSSecret';    

    /**
     * Let's stick a copy in the cache so we don't have to suffer the performance penalty over and over
     *
     * @return bool
     */
    protected function pushToCache($name=false, $value) {
        $cache     = new Memcache();
        $cache->connect($this->memcache_ip,$this->memcache_port);
        return $cache->set($name,(is_array($value) ? json_encode($value) : $value));
    }

    /** 
     * Let's try to get credentials from shared memory cache
     * 
     * @return array
     */
    protected function pullFromCache() {
        $results = [];
        if ($cache = new Memcache()) {
            if ($cache->connect($this->memcache_ip,$this->memcache_port)) {
               if ($settings = $cache->get($this->secrets_id)) {
                   $results = json_decode($settings,true);
               }
            }
        }
        return $results;
    }
    
    /**
     * Let's go get the credentials from the secrets repository
     *
     * @return array
     */
    protected function pullFromS3()  {
        $results        = [];
        $credentials    = '';
        $client =  new SecretsManagerClient([
            'version' => '2017-10-17',
            'region' => 'us-east-1'
        ]);
        try {
            if ($results = $client->getSecretValue(['SecretId' => $this->secrets_id])) {
                if (isset($results['SecretString'])) {
                    $credentials = $results['SecretString'];
                } else {
                    print("We got some secrets back, but its not in the format I am looking for.\n\n");
                    print_r($results);
                    die();
                }
            };
        } catch (AwsException $ex) {
            print("An Error has occurred: ".$ex->getAwsErrorCode()."\n\n");
        }
        self::pushToCache($this->secrets_id,$credentials);
        return json_decode($credentials,true);
    }
    
    public function __construct()    {
        if ($credentials = (($credentials = $this->pullFromCache()) ? $credentials : $credentials = $this->pullFromS3())) {
            $this->userid           = $credentials['username'];
            $this->password         = $credentials['password'];
            $this->database         = "dashboard";
            $this->dbhost           = $credentials['host'];
            $this->mongodb          = 'localhost:27017';
            $this->mongodbUserId    = '';
            $this->mongodbPwd       = '';
            $this->cacheHost        = '127.0.0.1:11211';
            $this->smtpUserName     = 'apikey';
            $this->smtpPassword     = '';
            $this->smtpHost         = '';
        }
    }
    
    public function reset() {
        
    }
    
    public function clearPassword()         { $this->password       = '';   }
    public function clearMongodbPassword()  { $this->mongodbPwd     = '';   }
    //Accessors/Mutators follow
    public function getUserid()             { return $this->userid;         }
    public function getPassword()           { return $this->password;       }
    public function getDatabase()           { return $this->database;       }
    public function getDBHost()             { return $this->dbhost;         }
    public function getMongoDB()            { return $this->mongodb;        }
    public function getMongoDBUserId()      { return $this->mongodbUserId;  }
    public function getMongoDBPassword()    { return $this->mongodbPwd;     }
    public function getSmtpUserName()       { return $this->smtpUserName;   }
    public function getSmtpPassword()       { return $this->smtpPassword;   }
    public function getSmtpHost()           { return $this->smtpHost;       }
    public function getCacheHost()          { return $this->cacheHost;      }
}

?>
