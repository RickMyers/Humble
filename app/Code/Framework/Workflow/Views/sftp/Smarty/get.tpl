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
        padding: 2px; background-color: lightcyan; color: #333; border: 1px solid #aaf; width: 90%
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
            <form name="config-sftp-get-form" id="config-sftp-get-form-{$data.id}" method="post" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$window_id}" />
                <fieldset style="padding: 10px; width: 600px; text-align: left">
                    <legend>Instructions</legend>
                    <div>
                        This stage will connect to a remote file server by Secure FTP (sFTP).  You can choose to download only files with certain extension, and/or only new files
                    </div>
                    <br />
                    <table>
                        <tr>
                            <td colspan='2'><br /><i>Connectivity</i></td>
                        </tr>
                        <tr>
                            <td align="right">Host: </td>
                            <td>
                                <input class='paradigm-config-form-field' type="text" name="host" id="config_sftp_get_host_{$data.id}" value="{if (isset($data.host))}{$data.host}{/if}" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Port: </td>
                            <td>
                                <input class='paradigm-config-form-field' type="text" name="port" id="config_sftp_get_port_{$data.id}" value="{if (isset($data.port))}{$data.port}{/if}" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Username: </td>
                            <td>
                                <input class='paradigm-config-form-field' type="text" name="username" id="config_sftp_get_username_{$data.id}" value="{if (isset($data.username))}{$data.username}{/if}" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Password: </td>
                            <td>
                                <input class='paradigm-config-form-field' type="text" name="password" id="config_sftp_get_password_{$data.id}" value="{if (isset($data.password))}{$data.password}{/if}" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan='2'><br /><i>Directories</i></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%">
                                    <tr>
                                        <td align="right">Remote: </td>
                                        <td>
                                            <input class='paradigm-config-form-field' type="text" name="remote_dir" id="config_sftp_get_remote_{$data.id}" value="{if (isset($data.remote_dir))}{$data.remote_dir}{/if}" />
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td align="right">Local: </td>
                                        <td>
                                            <input class='paradigm-config-form-field' type="text" name="local_dir" id="config_sftp_get_local_{$data.id}" value="{if (isset($data.local_dir))}{$data.local_dir}{/if}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Extension(s): </td>
                                        <td nowrap>
                                            <input placeholder=".txt,.xml,.json etc..." class='paradigm-config-form-field' type="text" name="extensions" id="config_sftp_get_extensions_{$data.id}" value="{if (isset($data.extensions))}{$data.extensions}{/if}" /> (optional)
                                        </td>
                                    </tr>                                      
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan='2'><br /><i>Event</i></td>
                        </tr>
                        
                        <tr>
                            <td colspan="2"><table style="width: 100%"><tr>
                            <td align="right">
                                Field Name: 
                            </td>
                            <td nowrap>
                                <input placeholder="sftp_get" type='text' name="event_field" class="paradigm-config-form-field" id="event_field-{$data.id}" value="{if (isset($data.event_field) && $data.event_field)}{else}{/if}" /> (optional)
                            </td>
                                    </tr></table>
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
    Form.intercept($('#config-sftp-get-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$window_id}");
</script>