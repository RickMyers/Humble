[{
    "text": "",
    "value": ""
}
{foreach from=$modules->fetch() item=module},{
        "text": "[{$module.namespace}] {$module.description}",
        "value": "{$module.namespace}"
    }
{/foreach}
]