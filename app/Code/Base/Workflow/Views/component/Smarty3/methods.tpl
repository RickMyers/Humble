{assign var=list value=$methods->fetch(true)}
(
     [{
        "text": "Please choose from one of the below",
        "value": '',
        "style": 'font-style: italic'
    }
{foreach from=$list item=method}
    ,{
        "text"  : "{$method.method}",
        "value" : "{$method.method}"
    }
{/foreach}
] )

