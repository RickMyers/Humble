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
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">Detector</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">Trigger</div></td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form name="trigger-detector-form" id="trigger-detector-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="windowId" id="windowId_{$data.id}" value="{$windowId}" />
                <input type="hidden" name="namespace" id="namespace_{$data.id}" value="paradigm" />
                <input type="hidden" name="component" id="component_{$data.id}" value="detector" />
                <input type="hidden" name="method" id="method_{$data.id}" value="trigger" />
                <table>
                    <tr><td>
                            <fieldset><legend>Instructions</legend>
                                This stage looks for variables present in the data stream and can trigger other workflows that are listening (or "sensing") for those variables.<br /><br />
                                What trigger(s) should we be detecting for:<br /><br />
                                <input type="checkbox" name="glucose" id="glucose_{$data.id}" {if (isset($data.glucose) && ($data.glucose == "on"))}checked{/if} />&nbsp;&nbsp;Glucose<br />
                                <input type="checkbox" name="blood_pressure" id="blood_pressure_{$data.id}" {if (isset($data.blood_pressure) && ($data.blood_pressure == "on"))}checked{/if} />&nbsp;&nbsp;Blood Pressure<br />
                                <input type="checkbox" name="heart_rate" id="heart_rate_{$data.id}" {if (isset($data.heart_rate) && ($data.heart_rate == "on"))}checked{/if} />&nbsp;&nbsp;Heart Rate<br />
                                <input type="checkbox" name="temperature" id="temperature_{$data.id}" {if (isset($data.temperature) && ($data.temperature == "on"))}checked{/if} />&nbsp;&nbsp;Temperature<br />
                                <input type="checkbox" name="other" id="other_{$data.id}" {if (isset($data.other) && ($data.other == "on"))}checked{/if} />&nbsp;&nbsp;Other:
                                <input type="text" name="field" id="field_{$data.id}" style="width: 120px; padding: 2px; background-color: lightcyan; border: 1px solid #aaf; border-radius: 2px" value="{if (isset($data.field) && ($data.field))}{$data.field}{/if}"/><br /> <br />
                                <input type="submit" value=" Save " />
                            </fieldset>
                        </td></tr>
                    <tr><td>
                        </td></tr>
                </table>

            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#trigger-detector-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$windowId}");
</script>