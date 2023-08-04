<table style="min-width: 600px; float: right">
    <tr>
        <th class="services-field-desc2">
            Parameter
        </th>
        <th class="services-field-desc2">
            Request Parameter
        </th>
        <th class="services-field-desc2">
            Source
        </th>
        <th class="services-field-desc2">
            Datatype
        </th>
        <th class="services-field-desc2">
            Required
        </th>
        <th class="services-field-desc2">
            Default
        </th>
    </tr>
    {assign var=parms value=$parameters->load()}
    {assign var=n value=\Log::general($parms)}
    {foreach from=$parms item=section}
         {assign var=n value=\Log::console($section)}
    {/foreach}
   
    {if ($parms['header'])}
        <div>Header</div>
        {foreach from=$parms.header item=val key=k}
            <div>{$k} = {$val}</div>
        {/foreach}
    {/if}
{foreach from=$parms['parameters'] key=service item=service}
    
    <tr style="background-color: {cycle values="#cecece,#d5d5d5"}">
        <td class="services-field2">{if ($service.parameter)}{$service.parameter}{/if}</td>
        <td class="services-field2">{if ($service.value)}{$service.value}{/if}</td>
        <td class="services-field2">{if ($service.source)}{$service.source}{/if}</td>
        <td class="services-field2">{if ($service.datatype)}{$service.datatype}{/if}</td>
        <td class="services-field2">{if ($service.required)}{$service.required}{/if}</td>
        <td class="services-field2">{if ($service.default)}{$service.default}{/if}</td>
        <td class="services-field2">{if ($service.description)}{$service.description}{/if}</td>
    </tr>
{foreachelse}
    <tr>
        <td colspan="5">
            <b>Requires No Parameters</b>
        </td>
    </tr>
{/foreach}
</table>
<div style="clear: both"><br /></div>
{*debug*}