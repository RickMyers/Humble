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
    .paradigm-config-form-field {
        padding: 2px; background-color: lightcyan; color: #333; border: 1px solid #aaf
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
            <form name="config-sftp-put-form" id="config-sftp-put-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="windowId" id="windowId_{$data.id}" value="{$windowId}" />
                <fieldset style="padding: 10px; width: 600px; text-align: left">
                    <legend>Instructions</legend>
                    <div>
                        This stage will connect to a remote file server by Secure FTP (sFTP).
                    </div>
                    <table>
                        <tr>
                            <td>Host:</td>
                            <td>
                                <input class='paradigm-config-form-field' type="text" name="host" id="config_sftp_put_host_{$data.id}" value="{if (isset($data.host))}{$data.host}{/if}" />
                            </td>
                        </tr>
                        <tr>
                            <td>Port:</td>
                            <td>
                                <input class='paradigm-config-form-field' type="text" name="host" id="config_sftp_put_port_{$data.id}" value="{if (isset($data.port))}{$data.port}{/if}" />
                            </td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td>
                                <input class='paradigm-config-form-field' type="text" name="host" id="config_sftp_put_username_{$data.id}" value="{if (isset($data.username))}{$data.username}{/if}" />
                            </td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>
                                <input class='paradigm-config-form-field' type="text" name="host" id="config_sftp_put_password_{$data.id}" value="{if (isset($data.password))}{$data.password}{/if}" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan='2'>Directories</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%">
                                    <tr>
                                        <td>Remote</td>
                                        <td>
                                            <input class='paradigm-config-form-field' type="text" name="host" id="config_sftp_put_remote_{$data.id}" value="{if (isset($data.remote))}{$data.remote}{/if}" />
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>Local</td>
                                        <td>
                                            <input class='paradigm-config-form-field' type="text" name="host" id="config_sftp_put_local_{$data.id}" value="{if (isset($data.local))}{$data.local}{/if}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Extension(s) (optional)</td>
                                        <td>
                                            <input class='paradigm-config-form-field' type="text" name="host" id="config_sftp_put_extensions_{$data.id}" value="{if (isset($data.extensions))}{$data.extensions}{/if}" />
                                        </td>
                                    </tr>                                      
                                </table>
                            </td>
                        </tr>
                    </table>
                 <br /><br >
                <input type="checkbox" name="new_files_only" id="new_files_only_{$data.id}" value="Y"
                       {if (isset($data.new_files_only) && ($data.new_files_only == "Y"))}
                              checked
                       {/if}
                /> New Files Only<br />
                <br /><input type="submit" value=" Save " />
                </fieldset>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    //Form.intercept(Form Reference,MongoDB ID,optional URL or just FALSE,Dynamic WindowID to Close After Saving);
    Form.intercept($('#config-sftp-put-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$windowId}");
</script>