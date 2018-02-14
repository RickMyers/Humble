[
    {
        "text": "Choose...",
        "value": "",
        "style": "font-style: italics"
    }
{foreach from=$templates->fetch() item=template}
    ,{
        "text": "{$template.description}",
        "value": "{$template.id}"
    }
{/foreach}
]