<?php
/**
    ____              __           
   / __ \____  __  __/ /____  _____
  / /_/ / __ \/ / / / __/ _ \/ ___/
 / _, _/ /_/ / /_/ / /_/  __/ /    
/_/ |_|\____/\__,_/\__/\___/_/     
                                   
 An alternative routing mechanism for non-Apache servers

RewriteRule ^index.html?(.*) /humble/home/index?message=$1 "[B= ?,L,NC,QSA]"
RewriteRule ^app/ - [L,NC]
RewriteRule ^lib/ - [L,NC]
RewriteRule ^images/ - [L,NC]
RewriteRule ^web/ - [L,NC]
RewriteRule ^docs/ - [L,NC]
RewriteRule ^pages/ - [L,NC]
RewriteRule ^admin$ /admin/home/page [NC,QSA,L]
RewriteRule ^admin/$ /admin/home/page [NC,QSA,L]
RewriteRule ^distro/(.*)? /distro.php?action=$1 [QSA,L]
RewriteRule ^js/([^/\.]+)? /loader.php?type=js&package=$1 [QSA,L]
RewriteRule ^mjs/([^/\.]+)/(.*)? /loader.php?type=mjs&namespace=$1&file=$2 [QSA,L]
RewriteRule ^css/([^/\.]+)? /loader.php?type=css&package=$1 [QSA,L]
RewriteRule ^edits/([^/\.]+)/([^/\.]+)? /loader.php?type=edits&n=$1&f=$2 [QSA,L]
RewriteRule ^ckeditor/(.*)? /app/Code/Framework/Paradigm/web/js/ckeditor/$1 [QSA,L]
RewriteRule ^ace/(.*)? /app/Code/Framework/Paradigm/web/js/ace/$1 [QSA,L]
RewriteRule ^api/([^/\.]+)/([^/\.]+)/(.*)?$ /api.php?n=$1&t=$2&m=$3 [QSA,L]
RewriteRule ^api/([^/\.]+)/([^/\.]+)?$ /api.php?n=$1&t=$2 [QSA,L]
RewriteRule ^hook/([^/\.]+)/([^/\.]+)? /hapi.php?n=$1&hook=$2 [QSA,L]
RewriteRule ^mapi/([^/\.]+)/([^/\.]+)/(.*)?$ /mapi.php?n=$1&t=$2&m=$3 [QSA,L]
RewriteRule ^mapi/([^/\.]+)/([^/\.]+)?$ /mapi.php?n=$1&t=$2 [QSA,L]
RewriteRule ^esb/(.*)? /iapi.php?uri=$1 [QSA,L]
RewriteRule ^([^/\.]+)/([^/\.]+)/(.*)?$ /index.php?humble_framework_namespace=$1&humble_framework_controller=$2&humble_framework_action=$3 [QSA,L]
RedirectMatch 404 app/Code/Framework/Humble/etc/public_routes.json
RedirectMatch 404 \.(xml|yaml|yml|project)$
ErrorDocument 404 /humble/home/404
*/

chdir('app');
require 'Environment.php';
$project = Environment::project();
//print_r($project);
chdir('..');
$URI = '/app/Code/Humble.project';
begin:
    $processed  = false;
    $parts      = explode('/',$URI);
    $static     = [
        'app'       => true,
        'lib'       => true,
        'images'    => true,
        'web'       => true
    ];
    $block      = [
        '.xml'      => true,
        '.yaml'     => true,
        '.yml'      => true,
        '.project'  => true,
        '.json'     => true
    ];
    $rules      = [
        'distro'    => 'distro.php?action=$1',
        'js'        => 'app/loader.php?type=js&package=$1',
        'mjs'       => 'app/loader.php?type=mjs&namespace=$1&file=$2',
        'css'       => 'app/loader.php?type=css&package=$1',
        'edits'     => 'app/loader.php?type=edits&n=$1&f=$2',
        'api'       => ['app/api.php?n=$1&t=$2','api.php?n=$1&t=$2&m=$3'],
        'hook'      => 'app/hapi.php?n=$1&hook=$2',
        'mapi'      => ['app/mapi.php?n=$1&t=$2','mapi.php?n=$1&t=$2&m=$3'],
        'esb'       => 'app/iapi.php?uri=$1'
    ];
    $redirects  = [
        'index.html'=> '/humble/home/index?message=$1',
        'admin'     => '/admin/home/page',
        'ace'       => '/app/Code/Framework/Paradigm/web/js/ace/$1',
        'ckeditor'  => '/app/Code/Framework/Paradigm/web/js/ckeditor/$1'
    ];
    if (isset($redirects[$parts[0]])) {
        print("\nI am redirecting\n");
        $processed = true;
        die();
    } else if (strpos($URI,'.') && isset($block[strtolower(strrchr($URI,'.'))])) {
        ob_start();        
        print('Blocking '.strrchr($URI,'.')."\n");
        $_GET                                   = [];
        $_GET['humble_framework_namespace']     = 'humble';
        $_GET['humble_framework_controller']    = 'home';
        $_GET['humble_framework_action']        = '404';
        require_once 'index.php';
        ob_end_flush();
        die();
    }
    
    
    if (!$processed) {
        
    }
