{if ($events->getNamespace() == '-1')}
    {assign var=junk value=$events->setNamespace('')}
{/if}
(
     [{
        "text": "Please choose from one of the below",
        "value": '',
        "style": 'font-style: italic'
    }
{foreach from=$events->fetch() item=event}
    ,{
        "text"  : "{$event.event}",
        "value" : "{$event.event}"
    }
{/foreach}
] )


