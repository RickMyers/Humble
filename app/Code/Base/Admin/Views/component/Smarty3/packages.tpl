[
    {
        "text": "Please select the package",
        "value": ""
    }
{foreach from=$packages->fetch() item=package}
    ,{
        "text": "{$package.text}",
        "value": "{$package.text}"
    }
{/foreach}
]