<?php
/***
       ____    __  __  _
      / __/__ / /_/ /_(_)__  ___ ____
     _\ \/ -_) __/ __/ / _ \/ _ `(_-<
    /___/\__/\__/\__/_/_//_/\_, /___/
                           /___/
	Required per application

*/
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


    public function __construct()    {
        $this->userid           = "&&USERID&&";
        $this->password         = "&&PASSWORD&&";
        $this->database         = "&&DATABASE&&";
        $this->dbhost           = '&&HOST&&';
        $this->mongodb          = '&&MONGO&&';
        $this->mongodbUserId    = '&&MONGOUSER&&';
        $this->mongodbPwd       = '&&MONGOPWD&&';
        $this->cacheHost        = '&&CACHE&&';
        $this->smtpHost     	= '';
        $this->smtpUserName     = '';
        $this->smtpPassword    	= '';
    }

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