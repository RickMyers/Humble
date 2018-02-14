
<?php

require "Humble.php";

$test = [
    "node1" => [
        "node2" => [
            "final" =>
                "Hello World!"
        ]
    ]
];

$t = "test.node1.node2.final";

$x = explode('.',$t);
print_r($x);
$s = '';
foreach ($x as $idx => $node) {
    $s .= (!$s) ? '$'.$node : "['".$node."']";
}
print($s);
eval('$a='.$s.';');
print($a);
print(eval($s.';'));

?>