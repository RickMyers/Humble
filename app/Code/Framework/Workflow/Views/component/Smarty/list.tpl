{assign var=list value=$components->fetch(true)}
[
    {
        "text": "Please choose from one of the below",
        "value": ""
    }
{foreach from=$list item=component}
    ,{
        "text"  : "{$component.component}",
        "value" : "{$component.component}"
    }
{/foreach}
]