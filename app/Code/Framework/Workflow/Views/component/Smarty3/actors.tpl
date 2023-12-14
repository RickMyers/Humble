[{
    "text": "Please choose from this list",
    "style": "font-style: italic",
    "value": ""
}
{foreach from=$actors->fetch() item=actor}
,{
    "text": "{$actor.actor} - {$actor.description}",
    "value": "{$actor.id}"
}
{/foreach}
]