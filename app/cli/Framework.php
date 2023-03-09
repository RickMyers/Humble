<?php
//------------------------------------------------------------------------------
// Describes the parameters intended to be read by each CLI function
//------------------------------------------------------------------------------
$parameters = [
    'module' => [
        'required' => [
            "short-name|fullname"=>'Error Message'
        ],
        'optional' => [
            "short-name|fullname"=>'Description'            
        ]
    ]
];
//------------------------------------------------------------------------------
//Command handler
//------------------------------------------------------------------------------
function processCommand($command=false,$commandDetails=[]) {
    global $args;
    if ($command) {
        switch ($command) {
            case "" :
                break;
            default :
                break;
        }
    } else {
        print("\nCommand not present or unreadable.  Please review syntax.\n");
    }
}

