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
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{if (isset($data.namespace))}{$data.namespace}{else}N/A{/if}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">{if (isset($data.namespace))}{$data.component}{else}N/A{/if}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">{if (isset($data.namespace))}{$data.method}{else}N/A{/if}</div></td>

    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form name="config-operation-form" id="config-operation-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$window_id}" />
                <input type="hidden" name="namespace" id="namespace_{$data.id}}" value="paradigm" />
                <input type="hidden" name="component" id="component_{$data.id}}" value="operation" />
                <input type="hidden" name="method" id="method_{$data.id}}" value="execute" />
                <fieldset style="padding: 10px; width: 600px; text-align: left"><legend>Instructions</legend>
                    Identify the parameters and arguments necessary to run this program<br /><br />
                    <table>
                        <tr>
                            <td style="padding-bottom: 3px">Working Directory: </td>
                            <td style="padding-bottom: 3px"><input class='paradigm-config-form-field' type="text" name="directory" id="config_directory_{$data.id}" value="{if (isset($data.directory))}{$data.directory}{/if}" />(<i>optional</i>)</td>
                        </tr>
                        <tr>
                            <td>Language</td>
                            <td>
                                <br />
                                <input type="radio" name="language" id="language-{$data.id}-php" value="php" /> PHP <br />
                                <input type="radio" name="language" id="language-{$data.id}-shell" value="./" /> Bash Shell <br />
                                <input type="radio" name="language" id="language-{$data.id}-java" value="java" /> Java<br />
                                <input type="radio" name="language" id="language-{$data.id}-js" value="js" /> JavaScript<br />
                                <input type="radio" name="language" id="language-{$data.id}-java" value="py" /> Python<br />
                                <input type="radio" name="language" id="language-{$data.id}-other" value="other" /> 
                                Other: <input type="text" class='paradigm-config-form-field' name="language_other" id="config_language_other_{$data.id}" style="width: 70px" value="{if (isset($data.language_other))}{$data.language_other}{/if}"/><br />
                                <br />
                            </td>
                        </tr>                        
                        <tr>
                            <td style="padding-bottom: 3px">Program Name: </td>
                            <td style="padding-bottom: 3px"><input class='paradigm-config-form-field' type="text" name="program" id="config_program_{$data.id}" value="{if (isset($data.program))}{$data.program}{/if}" /></td>
                        </tr>
                        <tr>
                            <td>Arguments:</td>
                            <td>
                                <input class='paradigm-config-form-field' type="text" name="arguments" id="config_arguments_{$data.id}" value="{if (isset($data.arguments))}{$data.arguments}{/if}" />(<i>optional</i>)
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 3px">Output Event Field: </td>
                            <td style="padding-bottom: 3px"><input class='paradigm-config-form-field' type="text" name="event_field" id="config_event_field_{$data.id}" value="{if (isset($data.event_field))}{$data.event_field}{/if}" />(<i>optional</i>)</td>
                        </tr>                        
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>   
                        <tr>
                            <td colspan="2"><input type="submit" value=" Save " /></td>
                        </tr>                        
                    </table>
                <br /><br />
                </fieldset>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    //Form.intercept(Form Reference,MongoDB ID,optional URL or just FALSE,Dynamic WindowID to Close After Saving);
    {if (isset($data.language))}
        $('input:radio[name=language]').val(['{$data.language}']);
    {/if}
    Form.intercept($('#config-operation-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$window_id}");
</script>