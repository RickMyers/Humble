<?php

$s = microtime(true);
require "Humble.php";
require "Environment.php";
require "Code/Framework/Humble/includes/Constants.php";
require "Code/Framework/Humble/includes/Custom.php";
try {
    $source = 'Code/Framework/Admin/Controllers/testie.xml';
    $dest   = 'Code/Framework/Admin/Controllers/testie2.xml';
    $n  = 'action';
    $a  = 'something';
    $xml = new DOMDocument('1.0');
    $xml->preserveWhiteSpace = true;
    $xml->formatOutput = true; 
    $xml->loadXML(file_get_contents($source));
    /*
            <mongo namespace="paradigm" class="elements" id='element'>
                <parameter name='id' source='post' default='' />
            </mongo>        */
    function recurseNodes(&$nodeList) {
        global $xml, $n, $a;
        $attrs = [
            'namespace' => 'paradigm',
            'class'     => 'elements',
            'id'        => 'element'
        ];
        $p     = [
            'name'      => 'id',
            'source'    => 'post',
            'default'   => ''
        ];
        foreach ($nodeList->childNodes as $node) {
            if ($node->localName == $n) {
                foreach ($node->attributes as $var => $val) {
                    if ($var === 'name') {
                        print("Really found it \n");
                        if ($val->nodeValue === $a) {
                            print("Absolutely this time \n");
                            $parm = $xml->createElement('parameter');
                            foreach ($p as $attr => $value) {
                                $at     = $xml->createAttribute($attr);
                                $at->value = $value;
                                $parm->appendChild($at);                                
                            }
                            $tag    = $xml->createElement('mongo');
                            foreach ($attrs as $attr => $value) {
                                $at     = $xml->createAttribute($attr);
                                $at->value = $value;
                                $tag->appendChild($at);
                            }
                            $tag->append($parm);
                            $node->append($tag);
                        }
                        
                        //print_r($nodeList);
                    }
                    //print_r($var); print_r($val);
                    //print((string)$var.' =====> '.$val."\n");
                }
            }
            if ($node && $node->hasChildNodes()) {
                recurseNodes($node);
            }            
           // print_r($node);            
        }
        
    }
    recurseNodes($xml);
    $xml->save($dest);
    //file_put_contents($dest,$xml->saveXML());
} catch (Exception $ex) {
    print("Exception Ocurred\n");
    print_r($ex);
} finally {
    //die();
}
print("Done: ".microtime(true)-$s."\n");

