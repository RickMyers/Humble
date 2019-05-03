[
    {
        "text": "Please select a templating engine",
        "value": ""
    }
{foreach from=$templaters->fetch() item=engine}
    ,{
        "text": "[{$engine.templater}] {$engine.description}",
        "value": "{$engine.templater}"
    }
{/foreach}
]