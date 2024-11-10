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
        <td colspan="3" valign="middle" align="center">
            <form name="terminus-form" id="config-terminus-form-{$id}">
                <input type="hidden" name="id" id="element-id-{$id}" value="{$id}" />
                <input type="hidden" name="window_id" id="window-id-{$id}" value="{$window_id}" />
                <table>
                    <tr>
                        <td>Return:&nbsp;&nbsp;&nbsp;<input type="radio" name="returns" checked="checked" id="terminus-value-true-{$id}" value='1' /> True &nbsp;&nbsp; <input type="radio" name="returns" id="terminus-value-false-{$id}" id="" value="0" /> False<br />
                           <br /> Cancel Bubble:&nbsp;&nbsp;&nbsp;<input type="checkbox" value="Y" name="cancel" id="terminus-cancel-bubble-{$id}" /><br /><br />
                        </td>
                    </tr>
                    <tr>
                        <td><input type="submit" value=" Save "></td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#config-terminus-form-{$id}').get(),'{$id}','/paradigm/element/update','{$window_id}');
</script>