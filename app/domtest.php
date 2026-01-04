<?php
print("again\n");
print('Starting'."\n");
require "Humble.php";
/*print("Caching Enabled: ".\Environment::cachingEnabled()."\n");
$x = Humble::entity('humble/users');
print_r($x);
die("Done\n\n");
$t = microtime(true);
//for ($i=0; $i<$loops; $i++) {
 //   $x = apache_getenv('caching');
//}
//print('Apache ENV Var ['.$loops.']: '.time() - $t."\n");

$t = microtime(true);
for ($i=0; $i<$loops; $i++) {
    $x = ini_get('caching');
}
print('PHP Ini ['.$loops.']: '.microtime(true) - $t."\n");

*/
$src = 'Code/Framework/Humble/Controllers/user.xml';
$data = file_get_contents($src);


$dom = new DOMDocument();
$xml = $dom->loadXML($data);
print_r($dom->getElementsByTagName('action'));
foreach ($dom->childNodes as $tag => $node) {
    print($tag."\n");
    print_r($node);
}
