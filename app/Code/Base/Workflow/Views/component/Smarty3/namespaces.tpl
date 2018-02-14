[{
    "text": "",
    "value": ""
},
{
    "text": "[core] Framework Workflows",
    "value": "core"
},
{
    "text": "Humble Application Workflows",
    "value": "humble"
}{foreach from=$modules->fetch() item=module},{
        "text": "[{$module.namespace}] {$module.description}",
        "value": "{$module.namespace}"
    }
{/foreach}
]