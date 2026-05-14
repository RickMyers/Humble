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
            <form name="config-redirect-form" id="config-redirect-form-{$data.id}" onsubmit="return false" style="margin-left: auto; margin-right: auto; width: 500px">
                <fieldset><legend>Instructions</legend>
                    <div style="margin-top: 20px; margin-bottom: 20px; text-align: justify">
                        Please specify where to redirect the user to below, as well as to whether we will need to URL Encode any data that is passed along with the redirect
                    </div>
                    <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                    <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$window_id}" />
                    <table style="cellspacing: 5px; cellpadding: 5px">
                        <tr>
                            <td style="text-align: right">URL: </td>
                            <td><input type="text" style="background-color: lightcyan; width: 450px; padding: 5px; border-radius: 5px; border: solid 1px #333" name="url" id="config_po_{$data.id}" value="{if (isset($data.url))}{$data.url}{/if}" /></td>
                        </tr>
                        <tr>
                            <td style="text-align: right; white-space: nowrap">URL Encode:</td>
                            <td><input type="checkbox" {if (isset($data['urlencode']) && ($data['urlencode']=='Y'))}checked="checked"{/if} style="background-color: lightcyan; border: 1px solid #333; padding: 2px; border-radius: 2px;" value='Y' id='config_redirect_encode_{$data.id}}' name='urlencode' /></td>
                        </tr>
                        <tr>
                            <td style="padding-top: 20px" colspan="2">
                                <input type="submit" value=" Save " />
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    //Form.intercept(Form Reference,MongoDB ID,optional URL or just FALSE,Dynamic WindowID to Close After Saving);
    Form.intercept($('#config-redirect-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$window_id}");
</script>
