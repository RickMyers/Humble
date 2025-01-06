<?php
//------------------------------------------------------------------------------
//Include things you want to do after the call to the templating mechanism
//
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

//------------------------------------------------------------------------------
//Print out the result of anything that may have been given access to the response
//
foreach (\Humble::response() as $idx => $output) {
    print($output);
}
?>