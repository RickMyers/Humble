[
    {
        "text": "",
        "value": ""
    }
    {foreach from=$components item=component}
    ,{
        "text": "{$component.component}",
        "value": "{$component.component}"
    }
    {/foreach}
]
