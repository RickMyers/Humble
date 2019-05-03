[
    {
        "text": "Please select the category",
        "value": ""
    }
{foreach from=$categories->fetch() item=category}
    ,{
        "text": "{$category.text}",
        "value": "{$category.text}"
    }
{/foreach}
]