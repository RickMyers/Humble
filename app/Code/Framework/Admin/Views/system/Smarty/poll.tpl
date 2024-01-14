{assign var=results value=$application->poll()}
{assign var=comma   value=false}
{
    {foreach from=$results item=result key=id}
        {if ($comma)},{/if}
        "{$id}": {$result|json_encode}{assign var=comma value=true}
    {/foreach}
}


