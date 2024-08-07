<div style="background-color: #333; color: ghostwhite">
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 19%; border-right: 1px solid #ccc' class="services-field-desc">URL</div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 49%; border-right: 1px solid #ccc' class="services-field-desc">Description</div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc' class="services-field-desc">Authorized</div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc' class="services-field-desc">Namespace</div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 9%; border-right: 1px solid #ccc' class="services-field-desc">Output</div>
</div>
<div styl="clear: both"></div
    {assign var=toggle value=""}
    {foreach from=$services->fetch() item=service}
><div style='background-color: {cycle values="#d3d3d3,#e0e0e0"}; border-bottom: 1px solid #ccc; padding: 0px 2px 0px 2px'>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 19%; border-right: 1px solid #ccc; text-align: left'>
        <div style="text-align: left" class="services-field"><span onclick="Services.parms({$service.id})" style="color: blue; font-size: 125%; cursor: pointer">/{$service.namespace}/{$service.controller}/{$service.action}</a></div>
    </div>
    <div style='box-sizing: border-box; display: inline-block; overflow: hidden; width: 49%; border-right: 1px solid #ccc; text-align: left'>
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
    <div style="clear: both;"></div>
    <div id="service-{$service.id}-parameters" style="display: none;">
    </div>
</div
{foreachelse}
    No Services
{/foreach}
>
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
