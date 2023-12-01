<style type="text/css">
    .secret_field_desc {
        padding-bottom: 15px; font-family: monospace; font-size: .85em; letter-spacing: 1px
    }
</style>
<table style="width: 100%; height: 100%">
    <tr>
        <td>
            <div style="margin-left: auto; margin-right: auto; width: 500px; padding: 20px; background-color: rgba(00,102,153,.8); color: ghostwhite">
                <form name="fetch_secret_form" id="fetch_secret_form" onsubmit="return false">
                    <fieldset><legend>Fetch A Secret</legend>
                        A secret is an encrypted piece of text that can be an API Key, account credential, or other important piece of information that needs to be kept confidential.
                        If setting an API Key for use in the mapping.yaml file, use the SM:// protocol to recover and decrypt your secret (Example: <i>api-key: SM://mySecretName</i>)<br /><br />
                        <select name="namespace" id="fetch_secret_namespace">
                            <option value=""> </option>
                            {foreach from=$modules->fetch() item=module}
                                <option value="{$module.namespace}"> {$module.namespace|ucfirst} </option>
                            {/foreach}
                        </select><br />
                        <div class="secret_field_desc">Namespace</div>
                        <select name="secret_name" id="fetch_secret_name">
                            <option value=""> </option>
                            {foreach from=$secrets->fetch() item=secret}
                                <option value="{$secret.id}"> {$secret.secret_name} </option>
                            {/foreach}
                        </select><br />
                        <div class="secret_field_desc">Secret Name</div>
                        <input type="text" name="secret_value" id="fetch_secret_value" value="" /><input type="button" value=" Update " name="update_secret_submit" id="update_secret_submit" />
                        <div class="secret_field_desc">Secret Value</div>
                        <input type="button" value=" Fetch Secret " name="fetch_secret_submit" id="fetch_secret_submit" />
                    </fieldset>
                </form>
            </div>
        </td>
    </tr>
</table>
<script type="text/javascript"> 
    (function () {
        new EasyEdits('/edits/humble/fetchsecret','fetchsecret');
    })();
</script>

