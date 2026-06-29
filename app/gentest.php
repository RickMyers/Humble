<?php
function loopit($evt) {
	for ($j=0; $j<=$evt; $j++) {
		yield $j;
	}
	yield null;
}
$event = 10;
foreach (loopit($event) as $x) {
	print($x."\n");
	if ($x === null) {
		break;
	}
}
print("Null Detected\n");

