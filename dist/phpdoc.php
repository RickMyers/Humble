<?php
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header('Content-disposition: attachment; filename="PHPDoc2.phar"');
print(file_get_contents('PHPDoc2.phar'));