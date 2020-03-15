<?php
//------------------------------------------------------------------------------
//Include things you want to do after the action has been invoked, but before 
//  the results are passed to the templater
$output = ob_get_clean();
if ($output) {
    \Log::console($output);
}
foreach (\Humble::response() as $idx => $output) {
    print($output);
}
?>