{assign var=data value=$element->load()}
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
<table style="width: 100%; height: 100%; border-spacing: 1px;">
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">{$data.type}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">{$data.shape}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$data.id}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{$data.namespace}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">{$data.method}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">{$data.component}</div></td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form name="trigger-sensor-form" id="trigger-sensor-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$helper->getWindowId()}" />
                <table>
                    <tr><td>
                        What trigger are we looking for:
                        <select name="trigger" id="trigger-event-{$data.id}" style="color: #333">
                            <option value="">Choose from available triggers</option>
                            {foreach from=$triggers->fetch() item=trigger}
                                <option value="{$trigger.id}" {if (isset($data.trigger) && ($data.trigger == $trigger.id))}selected{/if}>{$trigger.description}</option>
                            {/foreach}
                         </select>
                        </td></tr>
                    <tr><td>
                        <input type="submit" value=" Save " />
                        </td></tr>
                </table>

            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#trigger-sensor-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$helper->getWindowId()}");
</script>