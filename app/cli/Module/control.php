<?php
//------------------------------------------------------------------------------
// Describes the parameters intended to be read by each CLI function
//------------------------------------------------------------------------------
$parameters = [
    'u' => [
        'required' => [
            "ns|namespace"=>'Namespace or * is required'
        ],
        'optional' => [
            
        ]
    ],
    'e' => [
        'required' => [
            "ns|namespace"=>'Namespace or * is required'
        ],
        'optional' => [
                 
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
            case "u" :
                print("I will update! \n");
                break;
            default :
                break;
        }
    } else {
        print("\nCommand not present or unreadable.  Please review syntax.\n");
    }
}
