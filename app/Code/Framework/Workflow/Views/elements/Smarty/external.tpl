{assign var=id value=$manager->getId()}
{assign var=window_id value=$manager->getWindowId()}
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
                <input type="hidden" name="window_id" id="window-id-{$id}" value="{$window_id}" />
                <input type='hidden' name='namespace' value='Paradigm' />
                <input type='hidden' name='component' value='External' />
                <input type='hidden' name='method'    value='Connector' />
                <div style="width: 660px; padding: 20px; margin-left: auto; margin-right: auto; border: 1px solid #333; border-radius: 10px; background-color: rgba(202,202,202,.2)">
                    <fieldset style="padding: 20px 0px"><legend>Instructions</legend>
                        From here you can choose an external, partial (no trigger), workflow.  You can choose to run the workflow "inline", or forked as an external process.
                        Forking the workflow will disconnect the running of the workflow from the parent.  For more information, please see
                        <a href="https://humbleprogramming.com/pages/ExternalWorfklows.htmls" target="_BLANK" style="color: blue">External Workflows</a>.
                        <div style="padding-top: 30px">
                            Available Workflows for Off Page Connectors
                        </div>
                        <ul>
                            {foreach from=$available_partials->fetch() item=workflow}

                                <div title="{$workflow.description}"><input type="radio" name="partial-workflow" id="partial-workflow-{$workflow.id}}" value="{$workflow.id}" {if (isset($data['partial-workflow']) && ($data['partial-workflow']==$workflow.id))}checked="checked"{/if} /> {$workflow.namespace} - {$workflow.title}</div>

                            {/foreach}
                            <br /><br />
                            <input type="checkbox" name="fork_it" value="Y" />Run in separate thread (see instructions)<br /><br />
                            <input type="submit" value="save" />
                        </ul>
                    </fieldset>
                </div>

            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#config-external-form-{$id}').get(),'{$id}','/paradigm/element/update','{$window_id}');
</script>
