<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";
require "SQL.php";
$structure = 'Code/Framework/Humble/lib/syntax/Structure.xml';
$attributes     = 'Code/Framework/Humble/lib/syntax/Attributes.xml';
try {
    $tags   = [];
    $attrs  = [];
    $struct = simplexml_load_file($structure);
    $props  = simplexml_load_file($attributes);
    foreach ($props as $tag => $properties) {
        
        //Use recursion to convert to an array
        $attrs[$tag] = isset($attrs[$tag]) ? $attrs[$tag] : [];
        foreach ($properties as $prop => $attr) {
            $attrs[$tag][$prop] = $attr; 
        }
    }
    foreach ($struct as $tag => $properties) {
        $tags[$tag] = isset($tags[$tag]) ? $tags[$tag] : [ 'attributes' => [], 'children'=>[]];
        if ($attr       = $properties->attributes()) {
            $tags[$tag]['attributes'] = $attr;
        }
//        foreach ($properties as $x => $y) {
 //           $tags[$tag]['children'][$x] = isset($attrs[$tag]) ? $attrs[$tag] : [];
  //      }
    }
    print_r($tags);
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("Done: ".microtime(true)-$s."\n");
