<?php
/*______          __     __  __
 /_  __/__  _____/ /_   / / / /___ __________  ___  __________
  / / / _ \/ ___/ __/  / /_/ / __ `/ ___/ __ \/ _ \/ ___/ ___/
 / / /  __(__  ) /_   / __  / /_/ / /  / / / /  __(__  |__  )
/_/  \___/____/\__/  /_/ /_/\__,_/_/  /_/ /_/\___/____/____/
                 ___
                / __ \_____(_)   _____  _____
               / / / / ___/ / | / / _ \/ ___/
              / /_/ / /  / /| |/ /  __/ /
             /_____/_/  /_/ |___/\___/_/

Version 1.20160610

*------------------------------------------------------------------------------
* Interfaces, Includes, and Autoloader
*------------------------------------------------------------------------------*/
interface HarnessTeardown{
    public function execute();
}
interface HarnessStandup{
    public function execute();
}

require "Humble.php";

/*------------------------------------------------------------------------------
 * Global Variables
 *------------------------------------------------------------------------------*/
$help           = <<<HELP
/* -----------------------------------------------------------------------------
 *  This script is used to drive the test harness
 *
 *  Ex: Harness.php --option(s) source=other/driver.xml
 *
 *  option:
 *      --?, --h, This Help
 *      --X, --x, Execute Unit Test Drivers
 *      --I, --i, Generate information on unit tests
 *      --O, --o, Show the order the test packages will be executed
 *      --v, --V, Version
 * -----------------------------------------------------------------------------
 */
HELP;
$version        = '1.20160610';
$results        = [];
$summary        = [];
$packages       = [];
$dependencies   = [];
$source         = "tests/connect.xml";
$order          = [];
$list           = [];
$xref           = [];
$unit_tests     = [];
$output         = '';
$xml            = null;
$verbose        = false;
$standup        = false;
$teardown       = false;

/*------------------------------------------------------------------------------
 * Functions
 *------------------------------------------------------------------------------*/
function fetchParameter($parm,$list) {
    $value=false;
    foreach ($list as $key => $val) {
        if ($key == $parm) {
            $value = $val;
            break;
        }
    }
    return $value;
}
//------------------------------------------------------------------------------
function processArgs($args) {
    $parms = array();
    foreach ($args as $arg) {
        if (strpos($arg,'=')===false) {
            die('Invalid argument passed: '.$arg);
        }
        $arg = explode('=',$arg);
        $parms[$arg[0]] = $arg[1];
    }
    return $parms;
}
//------------------------------------------------------------------------------
function executeTest($package,$driver) {
    global $output;
    $results = [];
    if (file_exists($file = 'tests/'.$package.'/'.$driver.'Test.php')) {
        exec(Environment::getPHPLocation.' ../../phpunit/phpunit.phar '.$file,$results);
    } else {
        if ($output == 'JSON') {
            //NOP
        } else {
            print("#################################################################\n");
            print("# Missing Test Driver: ".$file."\n");
            print("#################################################################\n\n");
        }
    }
    return $results;
}

//------------------------------------------------------------------------------
function recurseHierarchy($data) {
    global $list, $order;
    foreach ($data as $idx => $d) {
        $order[] = $d;
        if (isset($list[$d])) {
            recurseHierarchy($list[$d]);
        }
    }
}

//------------------------------------------------------------------------------
//
//------------------------------------------------------------------------------
function sortThingsOut() {
    global $xml,$unit_tests,$dependencies,$list,$xref,$order,$standup,$teardown;
    foreach ($xml as $index) {
        $attr       = $index->attributes();
        $standup    = (isset($attr->standup))  ? (string)$attr->standup  : false;
        $teardown   = (isset($attr->teardown)) ? (string)$attr->teardown : false;
        foreach ($index as $package => $tests) {
            $attr                           = $tests->attributes();
            $dependencies[(string)$package] = (isset($attr['depends'])) ? $attr['depends'] : false;
            $unit_tests[(string)$package]   = [];
            foreach ($tests as $test) {
                $attr                           = $test->attributes();
                $unit_tests[(string)$package][] = ['class'=>(string)$attr['class'],'description'=>(string)$attr['description'],'namespace'=>(string)$attr['namespace']];
            }
        }
    }

    //Let's start building relationships between packages
    foreach ($dependencies as $package => $dependency) {
        //if nothing has a dependency, it gets to the front of the list of things to test
        if ((string)$dependency) {
            if (!isset($list[(string)$dependency])) {
                $list[(string)$dependency] = [];
            }
            $list[(string)$dependency][] = $package;
        }
        if (!(string)$dependency) {
            $order[] = (string)$package;
        } else {
            $xref[(string)$package] = (string)$dependency;
        }
    }

    $bases = [];
    //now look for the base dependencies
    foreach ($xref as $dependency => $package) {
        if (!isset($xref[$package])) {
            $bases[$package] = true;
        }
    }
    //We now know those base "packages" from which we will build our dependency tree
    foreach ($bases as $base => $bool) {
        foreach ($list[$base] as $start) {
            $order[] = $start;
            if (isset($list[$start])) {
                recurseHierarchy($list[$start]);
            }
        }
    }
    return (count($order)>0);
}

//------------------------------------------------------------------------------
//
//------------------------------------------------------------------------------
function outputResults($results) {
    print('Raw Output =============================================='."\n\n");
    foreach ($results as $row) {
        print("\t".$row."\n");
    }
    print("\nEnd =====================================================\n\n");
}

//------------------------------------------------------------------------------
//
//------------------------------------------------------------------------------
function executeTests() {
    global $order,$unit_tests,$xml,$summary,$verbose,$standup,$teardown;
    if ($standup) {
        $standup = ucfirst($standup);
        if (file_exists('tests/STANDUP/'.$standup.'.php')) {
            include_once('tests/STANDUP/'.$standup.'.php');
            $class = 'tests\\STANDUP\\'.$standup;
            $housekeeping = new $class();
            $housekeeping->execute();
        }
    }
    foreach ($order as $idx => $package) {
        //figure out test order here based on sequence.
        foreach ($unit_tests[$package] as $idx => $driver) {
            $results = executeTest($package, $driver['class']);
            if ($verbose) {
                outputResults($results);
            }
            if ($results && isset($results[2])) {
                $tests      = trim(substr($results[2],0,strpos($results[2],' ')));
                $coverage   = trim(substr($results[2],strpos($results[2],' ')));
                $summary[]  = [
                    'package' => $package, 'class' => $driver['class'], 'description' => $driver['description'], 'coverage' => $coverage, 'results' => str_split($tests,1), 'status' => $results[6]
                ];
            }
        }
    }
    if ($teardown) {
        $teardown = ucfirst($teardown);
        if (file_exists('tests/TEARDOWN/'.$teardown.'.php')) {
            if ($housekeeping) {
                unset($housekeeping);
            }
            include_once('tests/TEARDOWN/'.$teardown.'.php');
            $class = 'tests\\TEARDOWN\\'.$teardown;
            $housekeeping = new $class();
            $housekeeping->execute();
        }
    }
}

//------------------------------------------------------------------------------
//
//------------------------------------------------------------------------------
function loadXML() {
    global $xml, $source;
    $status = false;
    if (file_exists($source)) {
        $xml    = simplexml_load_string(file_get_contents($source));
        $status = !empty($xml);
    } else {
        print("#################################################################\n");
        print("# Missing Source Index: ".$source."\n");
        print("#################################################################\n\n");
    }
    return $status;
}

/*------------------------------------------------------------------------------
 * Main
 *------------------------------------------------------------------------------*/
main:
    ob_start();
    $args   = array_slice($argv,1);
    $error  = false;
    if ($args) {
        if (substr($args[0],0,2) === '--') {
            $cmd = substr($args[0],2);
            $parms = processArgs(array_slice($args,1));
            if (fetchParameter('source',$parms)) {
                $source = fetchParameter('source',$parms);
            }
            if (fetchParameter('output',$parms)) {
                $output = fetchParameter('output',$parms);
            }
            if (fetchParameter('verbose',$parms)) {
                $verbose = fetchParameter('verbose',$parms);
            }
            switch (strtolower($cmd)) {
                case "h"    :
                case "?"    :
                    print($help);
                    break;
                case "v"    :
                    print('Humble Framework Test Harness version '.$version);
                    break;
                case "o"    :
                    $ctr = 0;
                    if (loadXML()) {
                        if (sortThingsOut()) {
                            if ($output == 'JSON') {
                                print(json_encode($order));
                            } else {
                                print("\n".'Unit Test Packages will be processed in this order:'."\n\n");
                                foreach ($order as $package) {
                                    print("\t".++$ctr.") ".$package."\n");
                                }
                            }
                        }
                    } else {
                        $error = 'Unable process source file';
                    }
                    break;
                case "i"    :
                    break;
                case "x"    :
                    if (loadXML()) {
                        if (sortThingsOut()) {
                            executeTests();
                            if (strtoupper($output) == 'JSON') {
                                print(json_encode($summary));
                            } else {
                                print_r($summary);
                            }
                        }
                    } else {
                        $error = 'Unable process source file';
                    }
                    break;
                default     :
                    die('Dont know how to process that command ('.$cmd.')');
                    break;
            }
        }
    }
    if ($error) {
        print($error);
    }
end:
?>