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
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">File Trigger</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">Image</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$data.id}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{$manager->getNamespace()}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">File Trigger</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">N/A</div></td>
    </tr>
<!-- ################################ END HEADER SECTION ########################################-->    
    <tr>
        <td colspan="3" align="center" valign="middle">
            <!-- ########################## FORM SECTION ########################################-->
            <form name="config-file-trigger-form" id="config-file-trigger-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />                 <!-- Leave this As-Is -->
                <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$window_id}" />    <!-- Leave this As-Is -->
                <input type="hidden" name="workflow_id" id="workflow_id_{$data.id}" value="" />
                <fieldset style="padding: 10px 0px 10px 0px; width: 600px; text-align: left"><legend>Instructions</legend>
                    <div style="padding: 10px 0px 30px 0px; font-family: sans-serif">
                        Here you set the directory to watch, and optionally the file type.  If you set the eventName, then that event will be thrown when a file is added to this directory, or a matching file is changed.
                        You can also throw an event when a file is removed from this directory.  If this stage is part of a workflow, you do not need to throw any event as the workflow will execute when a file is changed or added.
                    </div>
                    <table class="w-full" style="width: 100%">
                        <tr>
                            <td>Directory: </td>
                            <td><input class='paradigm-config-form-field' type="text" name="directory" id="directory_{$data.id}" value="{if (isset($data.directory))}{$data.directory}{/if}" /></td>
                        </tr>
                        <tr>
                            <td>File Mask: </td>
                            <td><input class='paradigm-config-form-field' type="text" name="file_mask" id="file_mask_{$data.id}" value="{if (isset($data.file_mask))}{$data.file_mask}{/if}" /></td>
                        </tr>
                        <tr>
                            <td>Event Field: </td>
                            <td><input class='paradigm-config-form-field' type="text" name="field" id="field_{$data.id}" value="{if (isset($data.field))}{$data.field}{/if}" /></td>
                        </tr>
                        <tr><td colspan="2"><br /><hr /></td></tr>
                        <tr><td colspan="2"><br />Select the events to trigger on below:</td></tr>
                        <tr>
                            <td colspan="2"><input type="checkbox" name="new_file_event" id="new_file_event_{$data.id}" {if (isset($data.new_file_event) && ($data.new_file_event=="Y" ))}checked{/if} value="Y" /> On New File </td>
                        </tr>
                        <tr> 
                            <td colspan="2"><input type="checkbox" name="change_event" id="change_event_{$data.id}" {if (isset($data.change_event) && ($data.change_event=="Y" ))}checked{/if} value="Y" /> On Change </td>
                        </tr>                        
                        <tr>
                            <td colspan="2"><input type="checkbox" name="delete_event" id="delete_event_{$data.id}" {if (isset($data.delete_event) && ($data.delete_event=="Y" ))}checked{/if} value="Y" /> On Delete </td>
                        </tr>                        
                    </table><br /><br />
                    <fieldset style="padding: 10px"><legend>File Trigger Status</legend>
                        <input type="checkbox" name="active" id="active_{$data.id}" {if (isset($data.active) && ($data.active=="Y" ))}checked{/if} value="Y" />  - When this box is checked, the trigger is active
                    </fieldset>       
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
    Form.intercept($('#config-file-trigger-form-{$data.id}').get(),'{$data.id}','/workflow/file/update',"{$window_id}");
    console.log(Workflows);
    $('#workflow_id_{$data.id}').val(Workflows.activeDiagram());
</script>

