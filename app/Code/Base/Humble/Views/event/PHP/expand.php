<?php
function recurseIt($data) {
    foreach ($data as $idx => $node) {
        print('<li><i>'.$idx.'</i> =&gt; ');
        if (is_array($node)) {
            print('<ul>');
            recurseIt($node);
            print('</ul>');
        } else {
            print(htmlentities($node));
        }
        print('</li>');
    }
}
recurseIt($event->load());
?>
