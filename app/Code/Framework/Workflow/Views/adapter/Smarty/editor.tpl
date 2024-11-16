{assign var=data value=$element->load()}
<!--
    INSTRUCTIONS:

    This template makes setting up a configuration page for a workflow element pretty simple.
    
    You can leave most of this "as-is".  You can also tailor the template.tpl file to your liking.

    First you will need to copy this template to your configuration view file.

    In the FORM SECTION below, you will need to *ONLY* add the HTML input fields and field descriptions,
    along with any instructions for the person filling out the configuration page.  Also perform a change all
    on the 'js-adapter-form' placeholder with a unique name for the form element you are configuring.

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
<form name="config-js-adapter-form" id="config-js-adapter-form-{$window_id}" onsubmit="return false">
    <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />                 <!-- Leave this As-Is -->
    <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$window_id}" />    <!-- Leave this As-Is -->
    <div id="editor_control_{$window_id}">
        <fieldset id="editor_controls_{$window_id}"><legend>Resource Identifier</legend>
            <div style="float: right">
                <button id="editor_edit_{$window_id}" style="width: 50px; padding: 5px; border-radius: 10px; background-color: silver; color: #333; font-size: .8em; font-family: sans-serif"> Edit </button>
                <button id="editor_save_{$window_id}" style="width: 50px; padding: 5px; border-radius: 10px; background-color: silver; color: #333; font-size: .8em; font-family: sans-serif"> Save </button>
                <button id="editor_close_{$window_id}" style="width: 50px; padding: 5px; border-radius: 10px; background-color: silver; color: #333; font-size: .8em; font-weight: bolder; font-family: sans-serif"> Close </button>
            </div>
            <select name="resource_namespace" id="resource_namespace_{$window_id}" placeholder="Namespace" style="width: 140px">
                <option value=""></option>
                {foreach from=$modules item=module}
                    <option value="{$module.namespace}" {if (isset($data.resource_namespace) && ($data.resource_namespace == $module.namespace))}selected='selected'{/if}>{$module.namespace|ucfirst}</option>
                {/foreach}
            </select>
            <div style='position: relative; display: inline-block'>
                <select name="resource" id="resource_{$window_id}" placeholder="Resource" style="width: 140px">
                    <option value=""></option>
                </select><input type='text' name='resource_combo' id='resource_{$window_id}_combo' value='{if (isset($data.resource))}{$data.resource}{/if}' />
            </div>
        </fieldset>
<div id="js_editor_{$window_id}" style='border: 1px solid #333; overflow: auto'>
{if (isset($data.code))}{$data.code}{/if}
</div>
    </div>
</form>

<script>
    if (!ACEEditors) {
        var ACEEditors = {};
    }
    var win_{$data.id} = Desktop.window.list['{$window_id}'];
    win_{$data.id}.resize(function () {
        $('#js_editor_{$window_id}').height(win_{$data.id}.content.height() - $E('editor_controls_{$window_id}').offsetHeight);
        ACEEditors['{$window_id}'].resize();
    });
    ((win)=> {
        if ($('#namespace_{$window_id}').val()) {
            $('#namespace_{$window_id}').trigger('change');
        }
        $('#js_editor_{$window_id}').height(win.content.height() - $E('editor_controls_{$window_id}').offsetHeight);
        var editor = ACEEditors['{$window_id}'] = ace.edit("js_editor_{$window_id}");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/javascript");
        var xx = new EasyEdits('','new-app-{$window_id}');
        xx.fetch("/edits/paradigm/jseditor");
        xx.process(xx.getJSON().replace(/&&win_id&&/g,'{$window_id}'));        
        win.resize();
    })(win_{$data.id});
    //Example of intercepting the save event and redirecting to a specified URL.  This does the form magic.
    //Form.intercept(Form Reference,MongoDB ID,optional URL or just FALSE,Dynamic WindowID to Close After Saving);
    Form.intercept($('#config-js-adapter-form-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$window_id}");
</script>
