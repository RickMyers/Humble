<?php
//------------------------------------------------------------------------------
//Include things you want to do after the call to the templating mechanism
//\Log::console($_SESSION);
if ($ajaxUpload) {
    foreach ($_FILES as $file => $data) {
        if (gettype($data['tmp_name']) == 'array') {
            for ($i=0; $i<count($data['tmp_name']); $i++) {
                unlink($data['tmp_name'][$i]);
            }
        } else {
            unlink($data['tmp_name']);
        }
    }
}
$output = ob_get_clean();
if ($output) {
    \Log::console($output);
}
foreach (\Humble::response() as $idx => $output) {
    print($output);
}
?>