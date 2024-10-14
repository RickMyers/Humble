<div style="background-color: #333; color: ghostwhite">
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 19%; border-right: 1px solid #ccc' class="services-field-desc">URL</div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 39%; border-right: 1px solid #ccc' class="services-field-desc">Description</div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc' class="services-field-desc">Authorized</div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc' class="services-field-desc">Namespace</div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc' class="services-field-desc">Output</div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc' class="services-field-desc">Mapped</div>
</div>
<div style="clear: both"></div>
    {assign var=toggle value=""}
    {foreach from=$services->fetch() item=service}
<div style='background-color: {cycle values="#d3d3d3,#e0e0e0"}; border-bottom: 1px solid #ccc; padding: 0px 2px 0px 2px'>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 19%; border-right: 1px solid #ccc; text-align: left'>
        <div style="text-align: left" class="services-field"><span onclick="{*Services.parms({$service.id})*}$('#service-{$service.id}-parameters').slideToggle()" style="color: blue; font-size: 125%; cursor: pointer">/{$service.namespace}/{$service.controller}/{$service.action}</a></div>
    </div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 39%; border-right: 1px solid #ccc; text-align: left'>
        <div style="text-align: left" class="services-field" >{if (!$service.description)}N/A{else}{$service.description}{/if}</div>
    </div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc; text-align: center'>
        <div class="services-field">N</div>
    </div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc; text-align: center'>
        <div class="services-field">{$service.namespace} </div>
    </div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc; text-align: center'>
        <div class="services-field">{if (!isset($service.output))}text/html{else}{$service.output}{/if} </div>
    </div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc; text-align: center'>
        <div class="services-field">{if (!isset($service.mapped))}No{else}{$service.mapped}{/if} </div>
    </div>    
    <div style="clear: both;"></div>
    <div id="service-{$service.id}-parameters" style="display: none;">
        <div class="bg-black-300 text-white">
            Resources Used
        </div>
        <div class="pl-10">
        {if (count($service.models))}
            {foreach from=$service.models key=model item=stuff}
                <div class="inline-block border-2 rounded-md text-center cursor-pointer border-teal-900 w-[100]" onclick="Administration.code.explore('{$service.namespace}','Model','{$model}')" title="Model"><img src="/images/admin/logical_model.png" class="w-[60] m-auto"><div>{$model}</div></div>
            {/foreach}            
        {/if}
        {if (count($service.entities))}
            {foreach from=$service.entities key=entity item=stuff}
            <div class="inline-block border-2 rounded-md text-center cursor-pointer border-teal-900 w-[100]" onclick="Administration.code.explore('{$service.namespace}','Entity','{$model}')" title="Entity"><img src="/images/admin/entity.png" class="w-[60] m-auto"><div>{$entity}</div></div>
            {/foreach}
        {/if}
        {if (count($service.helpers))}
            {foreach from=$service.helpers key=helper item=stuff}
        <div class="inline-block border-2 rounded-md text-center cursor-pointer border-teal-900 w-[100]" onclick="Administration.code.explore('{$service.namespace}','Helper','{$model}')" title="Helper"><img src="/images/admin/helper.png" class="w-[60] m-auto"><div>{$helper}</div></div>                
            {/foreach}            
        {/if}        
        {*if (count($service.access))}
            {foreach from=$service.access key=access item=stuff}
                <div class="inline-block border-2 rounded-md text-center cursor-pointer border-teal-900 w-[100]" onclick="alert('{$service.namespace}')"><img src="/images/admin/access_control.png" class="w-[60] m-auto"><br />{$access}</div>                
            {/foreach}            
        {/if*}          
        </div>
        <div style="background-color: #333; color: ghostwhite; padding: 2px 8px; font-family: sans-serif; font-size: .95em">Parameters</div>
        <table style="width: 100%" cellborder="0" cellspacing="0">
            <tr>
                <th>Name</th>
                <th>Value</th>
                <th>Source</th>
                <th>Required</th>
                <th>Format</th>
                <th>Default</th>
            </tr>
        {foreach from=$service.parameters item=parm}
            <tr>
                <td>{$parm.name}</td>
                <td>{if (isset($parm.value))}{$parm.value}{/if}</td>
                <td>{if (isset($parm.source))}{$parm.source}{/if}</td>
                <td>{if (isset($parm.required))}{$parm.required}{/if}</td>
                <td>{if (isset($parm.format))}{$parm.format}{/if}</td>
                <td>{if (isset($parm.default))}{$parm.default}{/if}</td>
            </tr>            
        {foreachelse}
            <tr><td colspan='6'>No Parameters</td></tr>
        {/foreach}
        </table>
    </div>
</div>
{foreachelse}
    No Services
{/foreach}
<script type="text/javascript">
    Services.rows = {$services->_rowCount()};
    Services.fromRow = {$services->_fromRow()};
    Services.toRow = {$services->_toRow()};
    Services.pages = {$services->_pages()};
    Services.currentPage = {$services->_page()};
    $('#current-page').html(Services.currentPage);
    $('#from-row').html(Services.fromRow);
    $('#to-row').html(Services.toRow);
    $('#total-rows').html(Services.rows);
    $('#total-pages').html(Services.pages);
</script>
