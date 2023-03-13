<?php
require 'cli/CLI.php';
class System extends CLI 
{
    protected static function performCoreUpdate($distro,$changed,$insertions,$matched,$ignored,$merged,$app,$version) {
        ob_start();
        print("\nPATCH REPORT\n########################################################\n\nMatched Files: ".$matched."\n\nThe following files will be updated by this process:\n\n");
        print("\nThe following files are on the local manifest indicating they should be IGNORED in the patch:\n\n");
        foreach ($ignored as $idx => $file) {
            print(str_pad($idx+1,5,"0",STR_PAD_LEFT).") ".$file."\n");
        }
        print("\nThe following files are on the local manifest indicating they should be MERGED in the patch:\n\n");
        foreach ($merged as $idx => $file) {
            print(str_pad($idx+1,5,"0",STR_PAD_LEFT).") ".$file."\n");
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
        if (strtolower(scrub(fgets(STDIN))) === 'yes') {
            $app->version->framework = $version;
            file_put_contents('application.xml',$app->asXML());
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
            print("Now running update...\n\n");
            //updateModule(['ns=*']);
            chdir('..');
        } else {
            print("\n\nFramework update aborted.\n\n");
        }
    }
    //--------------------------------------------------------------------------
    protected static function evaluateCoreDifferences($app,$project,$version) {
        $local_manifest = (file_exists('app/Humble.local.manifest')) ? json_decode(file_get_contents('app/Humble.local.manifest'),true) : ['merge'=>[],'ignore'=>[]];   //Load the manifest that tells us what files to not update
        if (file_exists('app/Humble.local.manifest')) {
            print("\n\n".'Found Local Manifest file...'."\n\n");
        }
        if (!$local_manifest) {
            die("\n\nERROR: Could not read Humble.local.manifest.  Check to see it exists or if there is a parsing issue with the file\n\n");
        }

        file_put_contents('distro_'.$version.'/humble.zip',file_get_contents($project['framework_url'].'/distro/fetch'));                                               //Download the current source base
        $changed    = []; $insertions = []; $source = []; $contents = []; $ignore = []; $merge = []; $matched = 0;
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
            print("processing ".$file."\n");
            if (file_exists($file)) {
                if (isset($local_manifest['ignore'][$file]) && $local_manifest['ignore'][$file]) {
                    $ignore[] = $file;
                } else if (isset($local_manifest['merge'][$file]) && $local_manifest['merge'][$file]) {
                    $merge[]  = $file;
                } else if ($distro->getFromIndex($file_idx) != file_get_contents($file)) {
                    $changed[] = $file;
                } else {
                    $matched++;
                }
            } else {
                $insertions[] = $file;
            }
        }
        self::performCoreUpdate($distro,$changed,$insertions,$matched,$ignore,$merge,$app,$version);
        @unlink($dist_file);
    }
    //--------------------------------------------------------------------------
    public static function patchFrameworkCore() {
        if (file_exists('../Humble.project')) {
            $project = json_decode(file_get_contents('../Humble.project'),true);
        } else {
            die("\nHumble project file not found.\n");
        }
        if (file_exists('../application.xml')) {
            $app = simplexml_load_file('../application.xml');
        } else {
            die("\Application XML file not found\n");
        }
        $canonical = json_decode(file_get_contents($project['framework_url']."/distro/version"),true);
        $canon_version = (int)str_replace(".","",(string)$canonical['version']);
        $local_version = (int)str_replace(".","",(string)$app->version->framework);
        $helper = Humble::getHelper('humble/directory');
        print("\n\nRunning patching report on core framework to version ".$canonical['version'].", please wait...\n\n");
        $distro = 'distro_'.$canonical['version'];
        chdir('..');
        @mkdir($distro,0775,true);
        self::evaluateCoreDifferences($app,$project,$canonical['version']);
        $helper->purgeDirectory($distro,true);
        @rmdir($distro);
        chdir('app');
    }    
}



