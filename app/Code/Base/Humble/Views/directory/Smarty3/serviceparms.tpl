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
    {assign var=n value=\Log::console($parms)}
    {if ($parms['header'])}
        <div>Header</div>
        {foreach from=$parms.header item=val key=k}
            <div>{$k} = {$val}</div>
        {/foreach}
    {/if}
{foreach from=$parameters->load() item=service}
    
    {*<tr style="background-color: {cycle values="#cecece,#d5d5d5"}">
        <td class="services-field2">{$service.parameter}</td>
        <td class="services-field2">{$service.value}</td>
        <td class="services-field2">{$service.source}</td>
        <td class="services-field2">{$service.datatype}</td>
        <td class="services-field2">{$service.required}</td>
        <td class="services-field2">{$service.default}</td>
        <td class="services-field2">{$service.description}</td>
    </tr>*}
{foreachelse}
    <tr>
        <td colspan="5">
            <b>Requires No Parameters</b>
        </td>
    </tr>
{/foreach}
</table>
<div style="clear: both"><br /></div>
{debug}