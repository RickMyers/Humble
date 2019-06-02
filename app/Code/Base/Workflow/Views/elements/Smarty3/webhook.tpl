{assign var=data value=$element->load()}
<!--
    This template makes setting up a configuration page for a workflow element simple.
    
    You can leave most of this "as-is".  You can also tailor the template.tpl file to your liking.

    In the FORM SECTION below, you will need to *ONLY* add the HTML input fields and field descriptions,
    along with any instructions for the person filling out the configuration page.

    Some common examples of HTML form fields are below as aids in designing your confiruation page.

    The framework handles everything else.  Also note the examples below on how you add default values and
    provide values from the `data` array.  The data array contains the current information on how the element
    is currently configured.

-->
<!-- ################################ HEADER SECTION ############################################--> 
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
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">Web Hook</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">&nbsp;</div></td>
    </tr>
<!-- ################################ END HEADER SECTION ########################################-->    
    <tr>
        <td colspan="3" align="center" valign="middle">
            <!-- ########################## FORM SECTION ########################################-->
            <form name="config-webhook-form" id="config-webhook-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />                 <!-- Leave this As-Is -->
                <input type="hidden" name="windowId" id="windowId_{$data.id}" value="{$windowId}" />    <!-- Leave this As-Is -->
                <fieldset style="padding: 10px; width: 600px; text-align: left"><legend>Instructions</legend>
                    Here we configure a WebHook, also called a "Reverse-API".  An external resource or application is going
                    to send some data to a URI that you identify below, and that data will be converted into an event and 
                    propagated down the workflow you design below.  The URI has to be of the form <b>/hook/namespace/<i>yourwebhook</i></b> where
                    <i>yourwebhook</i> can be any descriptive text:<br /><br /><table>
                        <tr>
                            <td align="right">
                                URI:
                            </td>
                            <td>/hook/{$data.namespace}/<input placeholder="webhook" class='paradigm-config-form-field' type="text" name="webhook" id="webhook_{$data.id}" value="{if (isset($data.webhook))}{$data.webhook}{/if}" /><br /><br >
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                Event Field:
                            </td>
                            <td><input class='paradigm-config-form-field' type="text" name="field" id="field_{$data.id}" value="{if (isset($data.field))}{$data.field}{/if}" /><br /><br >
                            </td>
                        </tr>
                    </table>
                     
                    <fieldset style="padding: 10px"><legend>WebHook Status</legend>
                        <input type="checkbox" name="active" id="active_{$data.id}" {if (isset($data.active) && ($data.active=="Y" ))}checked{/if} value="Y" />  - When this box is checked, the webhook is available
                    </fieldset>                    
                    <br />
                    <br /><input type="submit" value=" Save " />
                </fieldset>
            </form>
            <!-- ########################## END FORM SECTION ####################################-->                
        </td>
    </tr>
</table>
<script type="text/javascript">
    //Example of intercepting the save event and redirecting to a specified URL.  This does the form magic.
    //Form.intercept(Form Reference,MongoDB ID,optional URL or just FALSE,Dynamic WindowID to Close After Saving);
    Form.intercept($('#config-webhook-form-{$data.id}').get(),'{$data.id}','/paradigm/webhook/save',"{$windowId}");
</script>
