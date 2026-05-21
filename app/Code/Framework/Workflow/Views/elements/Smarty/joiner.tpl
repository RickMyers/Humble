{assign var=data value=$element->load()}
<!--
    INSTRUCTIONS:

    This template makes setting up a configuration page for a workflow element pretty simple.
    
    You can leave most of this "as-is".  You can also tailor the template.tpl file to your liking.

    First you will need to copy this template to your configuration view file.

    In the FORM SECTION below, you will need to *ONLY* add the HTML input fields and field descriptions,
    along with any instructions for the person filling out the configuration page.  Also perform a change all
    on the 'file-trigger' placeholder with a unique name for the form element you are configuring.

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
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">Joiner</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">Circle</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$data.id}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{$manager->getNamespace()}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">Workflow</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">Manage</div></td>
    </tr>
<!-- ################################ END HEADER SECTION ########################################-->    
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form name="joiner-form" id="form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />                 <!-- Leave this As-Is -->
                <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$window_id}" />    <!-- Leave this As-Is -->
                <input type="hidden" name="workflow_id" id="workflow_id_{$data.id}" value="" />            
                <fieldset style="padding: 10px 0px 10px 0px; width: 600px; text-align: left"><legend>Instructions</legend>
                    <div style="padding: 10px 0px 30px 0px; font-family: sans-serif">
                        Joiners bring in the ability to implement iteration, and with that iteration there is a risk of causing an infinite loop, which in a cloud environment can be catastrophic.
                        Here we are going to set some limits, and actions to take if those limits are breached.  Below, set the number of times the workflow can loop through a joiner.  If that 
                        limit is exceeded, you can specify how to respond, whether to kill the workflow and possibly also to 
                    </div>
                    <table class="w-full" style="width: 100%">
                        <tr>
                            <td style="text-align: center"><input type="checkbox" name="protection" id="protection_{$data_id}" value="Y" {if (isset($data.protection) && ($data.protection=="Y"))}checked="checked"{/if}/></td>
                            <td> Enable Infinite Loop Protection</td>
                        </tr>                        
                        <tr>
                            <td>Threshold: </td>
                            <td><input class='paradigm-config-form-field' type="text" name="threshold" id="threshold_{$data.id}" value="{if (isset($data.threshold))}{$data.threshold}{else}100{/if}" /></td>
                        </tr>
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td colspan="2">Threshold is the number of iterations before terminating the workflow and signaling a critical error.  A threshold of zero (0) means there is no limit to the number of iterations allowed.  Default is 100.</td>
                        </tr>
                    </table><br /><br />
                    <br /><input type="submit" value=" Save " />
                </fieldset>            
            </form>
        </td>
    </tr>
</table>
        <script type="text/javascript">
    //Example of intercepting the save event and redirecting to a specified URL.  This does the form magic.
    //Form.intercept(Form Reference,MongoDB ID,optional URL or just FALSE,Dynamic WindowID to Close After Saving);
    Form.intercept($('#form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$window_id}");
    $('#workflow_id_{$data.id}').val(Workflows.activeDiagram());
</script>