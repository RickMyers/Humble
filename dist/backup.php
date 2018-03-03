<?php

if (isset($argv[1]) && (($argv[1] == 'save') || ($argv[1] == 'restore'))) {
        $action  = $argv[1];
        $sources = json_decode(file_get_contents('backup.json'),true);
        foreach ($sources[$action] as $module => $files) {
                print("\n\nProcessing ".$module." files...\n\n");
                foreach ($files as $target => $destination) {
                        print("\tSaving directory ".$target." to ".$destination."\n");
                        @mkdir($destination,0775,true);
                        exec("cp -R ".$target." ".$destination);
                        exec("chown -R codeship:www-data ".$destination);
                }
        }
} else {
        print("\nYou must pass in either 'save' or 'restore'\n\n");
}


