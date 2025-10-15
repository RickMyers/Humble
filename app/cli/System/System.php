<?php
require_once 'cli/CLI.php';
require_once 'Humble.php';
require_once 'Environment.php';
class System extends CLI 
{
    
    /**
     * Displays the system status
     * 
     * @return boolean
     */
    public static function status() {
        $xml    = \Environment::applicationXML();
        if ($xml->status->enabled == 1) {
            print("\n\n".date('Y-m-d H:i:s').'   <Application is enabled>'."\n\n");
            return true;
        } else {
            print("\n\n".date('Y-m-d H:i:s').'   <Application is disabled>'."\n\n");
            return false;
        }
    }

    /**
     * Adds a new user to Administrators table (default/users)
     */
    public static function addUser() {
        $args  = self::arguments();
        $uname = $args['user_name'];
        $passw = $args['password'];
        $first = $args['first_name'];
        $last  = $args['last_name'];
        $email = $args['email']?? null;
        $uid   = $args['uid']  ?? null;
        if ($uname && $passw) {
            Humble::entity('default/users')->newUser($uname,MD5($passw),$first,$last,$email,$uid);
        } else {
            print("Not enough data was passed to create a user.  user_name and password are minimum required fields.\n");
        }
    }
    /**
     * Toggles whether we are going to use local authentication or some form of SSO token
     */
    public static function toggleAuthentication() {
        $xml  = \Environment::applicationXML();
        $enabled = (int)$xml->status->SSO->enabled;
        $xml->status->SSO->enabled = $enabled ? 0 : 1;
        file_put_contents(\Environment::applicationXMLLocation(),$xml->asXML());
        $message = ($enabled) ? 'Authentication Engine: LOCAL' : 'Authentication Engine: SSO';
        print("\n\n".$message."\n\n");
    }
    
    /**
     * Clears the controller cache for all active modules
     * 
     * @return string
     */
    public static function clear() {
        $ctr = 0;
        foreach($modules = \Humble::entity('humble/modules')->setEnabled('Y')->fetch() as $module) {
            $cache_dir = 'Code'.DIRECTORY_SEPARATOR.$module['package'].DIRECTORY_SEPARATOR.$module['controller_cache'];
            if (is_dir($cache_dir)) {
                $dh = dir($cache_dir);
                while ($entry = $dh->read()) {
                    if (($entry == '.') || ($entry == '..')) {
                        continue;
                    }
                    @unlink($cache_dir.DIRECTORY_SEPARATOR.$entry);
                    $ctr++;
                }
            } 
        }
        return "Cached Items Cleared: ".$ctr;
    }
    
    /**
     * Toggles the application status
     */
    public static function toggle() {
        $xml  = \Environment::applicationXML();
        $enabled = (int)$xml->status->enabled;
        $xml->status->enabled = $enabled ? 0 : 1;
        file_put_contents(\Environment::applicationXMLLocation(),$xml->asXML());
        $message = ($enabled) ? 'System Status: OFFLINE' : 'System Status: ONLINE';
        print("\n\n".$message."\n\n");
    }
    
    /**
     * Increments the minor version of the application in the etc/application.xml file
     * 
     * @param int $next
     * @return string
     */
    public static function increment($next=1) {
        print("CHANGING VERSION"."\n");
        $framework  = (Environment::namespace()==='humble');
        $data       = \Environment::applicationXML();
        $v          = ($framework) ? explode('.',(string)$data->version->framework) : explode('.',(string)$data->version->app);
        for ($i=count($v)-1; $i>=0; $i-=1) {                                    //This is one of those ridiculously evil things in computer science
            $v[$i] = (int)$v[$i]+$next;
            if ($next  = ($v[$i]===10)) {
                $v[$i] = 0;
            }
        }
        $version = ($framework) ? $data->version->framework = (string)implode('.',$v) : $data->version->app = (string)implode('.',$v);
        file_put_contents(\Environment::applicationXMLLocation(),$data->asXML());
        return $version;
    }
        
    /**
     * Will take all files that are identifiable on the manifest and put them into a distro/zip
     */
    public static function package() {
        print("Processing Manifest...\n");
        $content = self::getManifestContent();
        print(count($content['files'])." files to add to package...\n");
        $xml     = \Environment::applicationXML();  
        chdir('..');
        foreach ($content['files'] as $file) {
            if (!isset($content['xref'][$file])) {
                $content['xref'][$file] = $file;
            }
        }
        @mkdir('../packages/',0775);
        $archive    = '../packages/Humble-Distro-'.(string)$xml->version->framework.'.zip';
        print("Creating archive ".$archive."\n");
        if (file_exists($archive)) {
            unlink($archive);
        }
        $zip = new ZipArchive();
        if ($zip->open($archive, ZipArchive::CREATE) !== true) {
            die('Wasnt able to create zip');
        };
        $ctr = 0;
        foreach ($content['xref'] as $src => $dest) {
            $exclude = false;
            if ((++$ctr % 100)===0) {
                print("Added ".$ctr." files\n");
            }
            foreach ($content['exclude'] as $mask => $type) {
                if (strpos($src,$mask) !== false) {
                    $exclude = true;
                }
            }
            if ($exclude) {
                continue;
            }
            if (file_exists($src) && is_file($src)) {
                $zip->addFile($src, $dest);
            }
        }
        //Now add manifest file in the form of a git ignore...
        $ignore = array_merge(['Docs/*','/images/*','/app/Constants.php','/app/vendor/*','**/cache/*','**/Cache/*','/app/Workflows'],array_keys($content['xref']));
        $ignore = array_merge(['app/cli/Component/*','app/cli/CLI.php','app/cli/Workflow/*','app/cli/Framework/*','app/cli/System/*','app/cli/Module/*'],$ignore);
        $zip->addFromString('.gitignore',implode("\n",$ignore));
        //$zip->addFromString('.manifest',implode("\n",$content['xref']));
        $zip->close();
        chdir('app');
    }
    
    /**
     * Takes a bunch of stuff and mixes it together
     * 
     * @param type $distro
     * @param type $changed
     * @param type $insertions
     * @param type $matched
     * @param type $ignored
     * @param type $merged
     * @param type $app
     * @param type $version
     */
    protected static function performCoreUpdate($distro,$changed,$insertions,$matched,$ignored,$app,$version) {
        ob_start();
        print("\nPATCH REPORT\n########################################################\n\nMatched Files: ".$matched."\n\nThe following files will be updated by this process:\n\n");
        print("\nThe following files are on the local manifest indicating they should be IGNORED in the patch:\n\n");
        $c=0;
        foreach ($ignored as $idx => $file) {
            print(str_pad(++$c,5,"0",STR_PAD_LEFT).") ".$file."\n");
        }
        print("\nThe following files will be patched:\n\n");
        foreach ($changed as $idx => $file) {
            print(str_pad($idx+1,5,"0",STR_PAD_LEFT).") ".$file."\n");
        }
        print("\nThe following files are new and will be inserted by this patch:\n\n");
        foreach ($insertions as $idx => $file) {
            print(str_pad($idx+1,5,"0",STR_PAD_LEFT).") ".$file."\n");
        }
        print($report = ob_get_clean());
        file_put_contents('patch_report.txt',$report);
        print("\n\nIf you do not want some files updated, add those files to the Humble.local.manifest file and re-run this process.\n\nA copy of the patch review report shown above can be found in file 'patch_report.txt'.\n\n");
        print("Do you wish to continue [yes/no]? ");
        if (strtolower(self::scrub(fgets(STDIN))) === 'yes') {
            $app->version->framework = $version;
            file_put_contents(Environment::applicationXMLLocation(),$app->asXML());
            foreach ($changed as $file) {
                file_put_contents($file,$distro->getFromName($file));
            }
            foreach ($insertions as $file) {
                if (count($parts = explode('/',$file))>1) {
                    @mkdir(implode('/',array_slice($parts,0,count($parts)-1)),0775,true);
                }
                file_put_contents($file,$distro->getFromName($file));
            }
            chdir('app');
            file_put_contents('Code/Framework/Humble/etc/config.xml',file_get_contents('https://humbleprogramming.com/distro/config'));
            @unlink('humble.bat');
            @unlink('humble');
            print("Now running update...\n\n");
            require 'cli/Module/Module.php';
            Module::update(['namespace'=>'*']);
            chdir('..');
        } else {
            print("\n\nFramework update aborted.\n\n");
        }
    }
    
    /**
     * Compares files in the downloaded distro with the file system and any exceptions identified in the Humble.local.manifest
     * 
     * @param type $app
     * @param type $project
     * @param type $version
     */
    protected static function evaluateCoreDifferences($app,$project,$version) {
        file_put_contents('distro_'.$version.'/humble.zip',file_get_contents($project->framework_url.'/distro/fetch'));                                               //Download the current source base
        $changed    = []; $insertions = []; $contents = []; $matched = 0;
        $ignore = [   //these files are removed after installation and should not be restored during a patch, but still need to be in the initial pull
            '.htaccess'=>true,
            'app/humble'=>true,
            'app/humble.bat'=>true
        ];
        $distro     = new ZipArchive();
        $dist_file = 'distro_'.$version.'/humble.zip';
        if ($distro->open($dist_file)) {
            for ($i=0; $i< $distro->numFiles; $i++) {
                $contents[] = $distro->getNameIndex($i);
            }
        } else {
            die("\nFailed To open distro zip file\n");
        }
        foreach ($contents as $file_idx => $file) {
            if (isset($ignore[$file]) && $ignore[$file]) {
                continue;
            }
            print("processing ".$file."\n");

            if (file_exists($file)) {
                if ($distro->getFromIndex($file_idx) != file_get_contents($file)) {
                    $changed[] = $file;
                } else {
                    $matched++;
                }
            } else {
                $insertions[] = $file;
            }
        }
        self::performCoreUpdate($distro,$changed,$insertions,$matched,$ignore,$app,$version);
        @unlink($dist_file);
    }
    //--------------------------------------------------------------------------
    public static function patch() {
        $project = \Environment::project();
        if ($project->name === 'Humble') {
            die("\nDo Not Run Patch On Framework!, use Git Pull\n\n");
        }
        $app     = \Environment::applicationXML();
        $canonical = json_decode(file_get_contents($project->framework_url."/distro/version"),true);
        $canon_version = (int)str_replace(".","",(string)$canonical['version']);
        $local_version = (int)str_replace(".","",(string)$app->version->framework);
        $helper = Humble::helper('humble/directory');
        print("\n\nRunning patching report on core framework to version ".$canonical['version'].", please wait...\n\n");
        $distro = 'distro_'.$canonical['version'];
        chdir('..');
        @mkdir($distro,0775,true);
        self::evaluateCoreDifferences($app,$project,$canonical['version']);
        $helper->purgeDirectory($distro,true);
        @rmdir($distro);
        chdir('app');
    }    

    //--------------------------------------------------------------------------
    public static function cloneTemplates() {
        $util = \Humble::model('humble/utility');
        $util->clone();
        print("\nTemplates Cloned... they are in lib/ directory of your main module\n\n");
    }
}



