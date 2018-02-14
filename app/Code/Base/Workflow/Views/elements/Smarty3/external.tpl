{assign var=id value=$manager->getId()}
{assign var=windowId value=$manager->getWindowId()}
{assign var=data value=$component->load()}
{assign var=returns value=$component->getReturns()}
<style type="text/css">
    .paradigm-config-descriptor {
        font-size: .8em; font-family: serif; letter-spacing: 2px;
    }
    .paradigm-config-field {
        font-size: 1em; font-family: sans-serif; text-align: right; padding-right: 4px;
    }
    .paradigm-config-cell {
        width: 33%; margin: 1px; background-color: #e8e8e8;  border: 1px solid #d0d0d0; padding-left: 2px
    }
</style>
<table style="width: 100%; height: 100%">
    {if ($data)}
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">{$data.type}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">{$data.shape}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$data.id}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{if (isset($data['namespace']))}{$data['namespace']}{else}N/A{/if}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">{if (isset($data['method']))}{$data['method']}{else}N/A{/if}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">{if (isset($data['component']))}{$data['component']}{else}N/A{/if}</div></td>
    </tr>
    {/if}
    <tr>
        <td colspan="3" valign="middle">
            <form name="external-form" id="config-external-form-{$id}">
                <input type="hidden" name="id" id="element-id-{$id}" value="{$id}" />
                <input type="hidden" name="windowId" id="window-id-{$id}" value="{$windowId}" />
                <input type='hidden' name='namespace'       id='webservice-namespace-{$manager->getId()}' value='Paradigm' />
                <input type='hidden' name='component'       id='webservice-component-{$manager->getId()}' value='External' />
                <input type='hidden' name='method'          id='webservice-method-{$manager->getId()}' value='Connector' />
                <div style="padding: 40px 20px 0px 40px">
                    <div style="">
                        Available Workflows for Off Page Connectors
                    </div>
                    <ul>
                        {foreach from=$available_partials->fetch() item=workflow}

                            <div title="{$workflow.description}"><input type="radio" name="partial-workflow" id="partial-workflow-{$workflow.id}}" value="{$workflow.id}" {if (isset($data['partial-workflow']) && ($data['partial-workflow']==$workflow.id))}checked="checked"{/if} /> {$workflow.namespace} - {$workflow.title}</div>

                        {/foreach}
                        <br />
                        <input type="submit" value="save" />
                    </ul>
                </div>

            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#config-external-form-{$id}').get(),'{$id}','/paradigm/element/update','{$windowId}');
</script>
