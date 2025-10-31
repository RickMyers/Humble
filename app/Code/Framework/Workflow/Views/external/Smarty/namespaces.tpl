[
    {
        "text": "",
        "value": ""
    }
    {foreach from=$namespaces item=dir}
    ,{
        "text": "{$dir.namespace}",
        "value": "{$dir.namespace}"
    }
    {/foreach}
]