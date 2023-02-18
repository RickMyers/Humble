<?php
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header('Content-disposition: attachment; filename="APIGen.phar"');
print(file_get_contents('APIGen.phar'));
