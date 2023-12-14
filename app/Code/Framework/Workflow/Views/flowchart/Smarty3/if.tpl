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
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">Paradigm</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">Flowchart</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">If</div></td>

    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form name="config-if-form" id="config-if-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="windowId" id="windowId_{$data.id}" value="{$windowId}" />
                <table>
                    <tr>
                        <td>
                            <fieldset style="width: 600px; padding: 10px"><legend>Instructions</legend>
                                This will create a standard "IF" symbol (statement) in the flowchart.<br /><br />
                                The field on the left is the <i>Event Field</i>, and this identifies the field to be compared upon inside the triggering Events data.  The field on the right is the <i>Comparison Value</i>, and this is what to compare against.
                                In the middle drop down box, select which comparison operater to use.<br /><br />
                                <table>
                                    <tr>
                                        <td><input type="text" name="field" id="config_field_{$data.id}" value="{if (isset($data.field))}{$data.field}{/if}" style="background-color: lightcyan; padding: 2px; border: 1px solid #aaf; width: 140px" /></td>
                                        <td>
                                            <select name="operator" id="config_operator_{$data.id}" style="background-color: lightcyan; padding: 2px; border: 1px solid #aaf; width: 140px">
                                                <option value="==">Equal To (==)</option>
                                                <option value="===">Strictly Equal (===)</option>
                                                <option value=">">Greater Than (&gt;)</option>
                                                <option value="<">Less Than (&lt;)</option>
                                                <option value=">=">Greater Than or Equal To (&gt;=)</option>
                                                <option value="<=">Less Than or Equal To (&lt;=)</option>
                                                <option value="!=">Not Equal To (!=))</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="value" id="config_value_{$data.id}" value="{if (isset($data.value))}{$data.value}{/if}" style="background-color: lightcyan; padding: 2px; border: 1px solid #aaf; width: 140px" /></td>
                                    </tr>
                                    <tr>
                                        <td>Field</td>
                                        <td>Operator</td>
                                        <td>Value</td>
                                    </tr>
                                </table><br /><br />
                                <input type="submit" value=" Save " />
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    //Form.intercept(Form Reference,MongoDB ID,optional URL or just FALSE,Dynamic WindowID to Close After Saving);
    Form.intercept($('#config-if-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$windowId}");
    {if (isset($data.operator))}
        $('#config_operator_{$data.id}').val('{$data.operator}')
    {/if}
</script>