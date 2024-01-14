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
            <form name="config-file-copy-form" id="config-file-copy-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="windowId" id="windowId_{$data.id}" value="{$windowId}" />
                <table>
                    <tr><td>
                            Source File: <input style="background-color: lightcyan; border: 1px solid #aaf; padding: 2px; border-radius: 2px; width: 140px" type="text" name="source" id="config_source_{$data.id}" value="{if (isset($data.source))}{$data.source}{/if}" /><br /><br >
                            Destination File: <input style="background-color: lightcyan; border: 1px solid #aaf; padding: 2px; border-radius: 2px; width: 140px" type="text" name="destination" id="config_destination_{$data.id}" value="{if (isset($data.destination))}{$data.destination}{/if}" /><br /><br >
                            <input type="checkbox" name="timestamp_file" id="timestamp_file_{$data.id}" value="Y"
                                   {if (isset($data.timestamp_file))}
                                       {if ($data.timestamp_file == "Y") }
                                           checked
                                       {/if}
                                   {/if}
                            /> Timestamp File
                        </td></tr>
                    <tr><td>
                            <br />
                        <input type="submit" value=" Save " />
                        </td></tr>
                </table>

            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#config-file-copy-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$windowId}");
</script>
