<?php

$current_dir = getcwd();
$config      = json_decode(file_get_contents('cleanup.json'),true);
if (!$config) {
        die('Could not read configuration JSON.');
}

$args = array_slice($argv,1);

$project = $args[0];
$ts      = $args[1];

$project_dir = $config['backup_dir'].$project;
chdir($project_dir);

//accumulate all the files and directories in the backup folder
$files       = [];
$dirs        = [];
$handler     = dir('.');
while ($entry = $handler->read()) {
    if (($entry == '.') || ($entry == '..') || is_dir($entry)) {
        if (is_dir($entry) && ($entry != '.') && ($entry != '..')) {
            $dirs[filemtime($entry)] = $entry;
        }
        continue;
    }
    $files[filemtime($entry)] = $entry;
}
krsort($files);
krsort($dirs);

//Let's convert vectors into arrays
$ctr = 0;
foreach ($files as $ts => $file) {
        unset($files[$ts]);
        $files[$ctr] = $file;
        $ctr++;
}
$ctr = 0;
foreach ($dirs as $ts => $dir) {
        unset($dirs[$ts]);
        $dirs[$ctr] = $dir;
        $ctr++;
}

//Now any more than allowed are removed
if (count($files) > $config['retain']) {
    for ($i=$config['retain']; $i<count($files); $i++) {
        print("Deleting File ".$files[$i]."\n");
        unlink($files[$i]);
    }
}
if (count($dirs) > $config['retain']) {
    for ($i=$config['retain']; $i<count($dirs); $i++) {
        print("Removing Directory ".$dirs[$i]."\n");
        shell_exec('rm -R '.$dirs[$i]);
    }
}
chdir($current_dir);
?>
