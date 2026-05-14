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
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">{$data.component}</div></td>        
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">{$data.method}</div></td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <div style='margin-left: auto; margin-right: auto; width: 500px; padding: 20px'>
                <form name="config-userdata-form" id="form-{$data.id}" onsubmit="return false">
                    <fieldset><legend>Instructions</legend>
                        <div style='padding-top: 20px; padding-bottom: 20px; text-align: justify; background-color: rgba(55,55,55,.2)'>
                            Please specify the field on the event that contains the user name or user id to get the data for, and then identify the name of event field to attach the data to
                        </div>
                        <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                        <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$window_id}" />
                        <table style='width: 100%'>
                            <tr>
                                <td>
                                    Source Field:
                                </td>
                                <td>
                                    <input style='padding: 5px; border-radius: 5px; background-color: lightcyan; border: 1px solid #333' type="text" name="source" id="data-source-{$data.id}" value="{if (isset($data.source))}{$data.source}{/if}" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Attach To:
                                </td>
                                <td>
                                    <input style='padding: 5px; border-radius: 5px; background-color: lightcyan; border: 1px solid #333' type="text" name="field" id="attach-field-{$data.id}" value="{if (isset($data.field))}{$data.field}{/if}" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan='2' style='padding-top: 20px'>
                                    <input type="submit" value=" Save " />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
            </div>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$window_id}");
</script>

