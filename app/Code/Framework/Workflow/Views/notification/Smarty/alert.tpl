{assign var=data value=$element->load()}
<style type="text/css">
    .paradigm-alert-descriptor {
        font-size: .8em; font-family: serif; letter-spacing: 2px;
    }
    .paradigm-alert-field {
        font-size: 1em; font-family: sans-serif; text-align: right; padding-right: 4px;
    }
    .paradigm-alert-cell {
        width: 33%; margin: 1px; background-color: #e8e8e8;  border: 1px solid #d0d0d0; padding-left: 2px
    }
</style>
<table style="width: 100%; height: 100%; border-spacing: 1px;">
    <tr style="height: 30px">
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Type</div><div class="paradigm-alert-field">{$data.type}</div></td>
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Shape</div><div class="paradigm-alert-field">{$data.shape}</div></td>
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Mongo ID</div><div class="paradigm-alert-field">{$data.id}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Namespace</div><div class="paradigm-alert-field">{$data.namespace}</div></td>
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Component</div><div class="paradigm-alert-field">{$data.component}</div></td>
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Method</div><div class="paradigm-alert-field">{$data.method}</div></td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form name="config-alert-form" id="config-alert-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$helper->getWindowId()}" />
                <table>
                    <tr><td>
                        <textarea style="background-color: lightcyan; padding: 2px; border: 1px solid #aaf; border-radius: 2px; width: 400px; height: 100px" name="message" id="config-alert-{$data.id}" >{if (isset($data.message))}{$data.message}{/if}</textarea><br />
                        Message:<br/>
                        <br />
                        Priority:&nbsp;&nbsp;&nbsp;<input style="background-color: lightcyan" type="radio" name="priority" {if ((isset($data.priority))&&($data.priority == 'normal'))}checked{/if} value="normal" id="config-alert-normal-{$data.id}" /> Normal
                        <input style="background-color: lightcyan" type="radio" name="priority" {if ((isset($data.priority))&&($data.priority == 'warning'))}checked{/if} value="warning" id="config-alert-warning-{$data.id}" /> Warning
                        <input style="background-color: lightcyan" type="radio" name="priority" {if ((isset($data.priority))&&($data.priority == 'high'))}checked{/if} value="high" id="config-alert-high-{$data.id}"/> High<br />
                        <br />
                        </td></tr>
                    <tr><td valign="top">
                        <input type="submit" value=" Save " />
                        </td></tr>
                </table>

            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#config-alert-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$helper->getWindowId()}");
</script>