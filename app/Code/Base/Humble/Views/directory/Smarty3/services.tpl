<div>
    <div style='float: left; overflow: hidden; width: 14%; border-right: 1px solid #ccc' class="services-field-desc">URL</div>
    <div style='float: left; overflow: hidden; width: 34%; border-right: 1px solid #ccc' class="services-field-desc">Description</div>
    <div style='float: left; overflow: hidden; width: 7%; border-right: 1px solid #ccc' class="services-field-desc">Authorized</div>
    <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc' class="services-field-desc">Namespace</div>
    <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc' class="services-field-desc">Controller</div>
    <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc' class="services-field-desc">Service</div>
    <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc' class="services-field-desc">Output</div>
    <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc' class="services-field-desc">View</div>
</div>
    {assign var=toggle value=""}
    {foreach from=$services->fetch() item=service}
        <div style='background-color: {cycle values="#d3d3d3,#e0e0e0"}; border-bottom: 1px solid #ccc; padding: 0px 2px 0px 2px'>
            <div style='float: left; overflow: hidden; width: 14%; border-right: 1px solid #ccc; text-align: left'>
                <div style="text-align: left" class="services-field"><span onclick="Services.parms({$service.id})" style="color: blue; cursor: pointer">{$service.URL|strtolower}</a></div>
            </div>
            <div style='float: left; overflow: hidden; width: 34%; border-right: 1px solid #ccc; text-align: left'>
                <div style="text-align: left" class="services-field" title="{$service.description}">{$service.description}</div>
            </div>
            <div style='float: left; overflow: hidden; width: 7%; border-right: 1px solid #ccc'>
                <div class="services-field">{$service.authorized} </div>
            </div>
            <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc'>
                <div class="services-field">{$service.namespace} </div>
            </div>
            <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc'>
                <div class="services-field">{$service.router} </div>
            </div>
            <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc'>
                <div class="services-field">{$service.service} </div>
            </div>
            <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc'>
                <div class="services-field">{$service.output} </div>
            </div>
            <div style='float: left; overflow: hidden; width: 8%; border-right: 1px solid #ccc'>
                <div class="services-field">{$service.view} </div>
            </div>
            <div style="clear: both;"></div>
            <div id="service-{$service.id}-parameters" style="display: none;">
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
