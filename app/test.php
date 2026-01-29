<?php
$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";
require "cli/Component/Component.php";
//$x = new Component();
//print_r($x);

//$x::check('humble','user');

$x = Humble::entity('humble/user/identification');

print('<pre>');
//$x->_rows(5)->_page(1)->fetch();
print_r($x->rows(5)->page(1)->fetch());
die();
//
$y =  $x->setId(1)->load();

//print_r($y->getArrayCopy());
/*
for ($i=0; $i<100; $i++) {
    $x->_rows(10)->_page(1)->setFirstName('Rick')->fetch();
}
 */

print("In: ".microtime(true)-$s."\n");
die();
/*$h = getcwd();
chdir('/var/www/tools');
print(getcwd()."\n");
require_once 'Test.php';
$p = new Test();
print_r($p);
chdir($h);*/
//$updater = Environment::getUpdater();

//$updater->scanExternalDirectories();

//print_r(Humble::entity('paradigm/system/events')->setActive('Y')->fetch());
//print_r(Humble::entity('paradigm/file/log')->fetch());
//print($arr = Humble::entity('humble/users')->setId(1)->load());
//print($arr."\n");

//suprise suprise... redis wins! (by a tiny bit)
/*$engines = ['MEMCACHE','REDIS'];
$runs    = 40000;
$data    = ['Scooby','Shaggy','Velma','Daphne','Fred','Scrappy'];
foreach ($engines as $engine) {
    print("Testing ".$engine."...\n");
    $start = time();
    Humble::cacheEngine($engine);
    foreach ($data as $token) {
        Humble::cache($token,$token);
    }
    for ($i=0; $i<$runs; $i++) {
        foreach ($data as $token) {
           $x =  Humble::cache($token);
        }
    }
    print("Took ".(time()-$start)." Seconds\n");
}
*/




//file_put_contents('framework.zip',file_get_contents('https://humbleprogramming.com/distro/fetch'));
/*
$x = time();
$p=Humble::push('testEvent',['moe','larry','curly']);
print_r($p);
print(time()-$x."\n");*/

/*$root       = Environment::getRoot('humble');
$rain       = Environment::getInternalTemplater($root.'/lib/sample/install','xml');
print_r($rain);
*/
//$mail_test = Humble::helper('humble/email');
//print_r($mail_test);
/*
$args = [
    "ash" => "cat",
    "ian" => "poodle",
    "bordeaux" => "evil"
];

Humble::spawn('testp.php',$args,'/var/www/test');
 * 
 */
//$USE_REDIS = true;
//Humble::cache('foo','bar');
//print(Humble::cache('foo')."\n");
//print(Humble::cache('foo',null)."\n");
//print(Humble::cache('foo')."\n");
//Humble::findCallByURI('workflow','');
//Humble::model('paradigm/system')->runFileLauncher();
//print_r(Humble::entity('paradigm/job/queue')->setId(9)->load());
//$lookup = Humble::model('usps/whatever');
//print($lookup->setAddress1('4120 Browndeer Circle')->setCity('Las Vegas')->setState('NV')->zipCodeLookup());
//print_r(Humble::entity('humble/users')->_rows(5)->_page(1)->list());
    //    $memory = Humble::helper('admin/system')->serverMemoryUsage();
  //      print_r($memory);
//print(explode(' ',file_get_contents('/proc/loadavg'))[0]."\n");
//print_r(Environment::getApplication());
//print_r(Humble::entity('admin/users')->_polyglot(true)->setId(1)->information());
//print_r(Humble::entity('paradigm/file/triggers')->fetch());
/*
$fp         = fopen('../../logs/cadence.log', "r");
$size       = filesize('../../logs/cadence.log');
$howmuch    = $size > 20000 ? 20000 : $size;
fseek($fp, -1*$howmuch, SEEK_END);
if ($data = fread($fp,$howmuch)) {
    $data = explode("\n",$data);
    for ($i=count($data)-1; $i>=0; $i--) {
        print($data[$i]."\n");
    }
}


*/
//print_r(Environment::getApplication('exceptions'));
//Humble::emit('doSomething',['moe'=>1,'larry'=>2,'curly'=>3]);
/*$faker = Humble::model('humble/faker');

for ($i=0; $i<20; $i++) {
    //print($faker->firstName().' '.$faker->lastName().' likes '.$faker->fullName()."\n");
    print('BDAY: '.$faker->date('10/4/2018','12/10/2019').', '.$faker->number(6,100,4000).' '.$faker->streetAddress().', '.$faker->city('FL').', '.$faker->state().', '.$faker->zipCode().' PN: '.$faker->phoneNumber(true)  ."\n");
}
*/
//$monitor = Humble::model('admin/monitor');
//$monitor->clear();print("\n");
//print_r($monitor->snapshot());
//print_r(Environment::getUpdater()->moduleEntities('humble'));
//print('Setting: '.Environment::flag('display_mysql_errors')."\n");
//Humble::model('admin/menus')->sort();


//$manager = Humble::model('humble/manager');

//$manager->createLandingPage(Environment::getProject());

//$emailer = Humble::helper('humble/email');
//$emailer->sendEmail(['rick@humbleprogramming.com'],'this is a test email','and what a test it is!','rick@humbleprogramming.com','rick@humbleprogramming.com');

//$emailer->sendEmail('rick@humbleprogramming.com','this is a test','and what a test it is!');
 /*   $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'http://humble.com:9080/humble/system/status');

    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

    $content = curl_exec ($ch);

    curl_close ($ch); 

    echo $content;
die();*/
//$x = fopen('http://humble.com/humble/system/status?sessionId=4pveh3flvet2kn8m95f1gcjeqd','r');
//print_r($x);

//$x = Humble::model('humble/monitor');
//$x->snapshot();

//$cadence = Humble::model('humble/cadence');
//$x = ($cadence->check() ? print('Running') : print('Idle')).print("\n");
//print($cadence->start());
//print($cadence->stop());
//print($cadence->reload());

