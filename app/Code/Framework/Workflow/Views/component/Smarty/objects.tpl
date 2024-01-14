{assign var=list value=$objects->fetch(true)}
(
     [{
        "text": "Please choose from one of the below",
        "value": '',
        "style": 'font-style: italic'
    }
{foreach from=$list item=object}
    ,{
        "text"  : "{$object.component}",
        "value" : "{$object.component}"
    }
{/foreach}
] )

