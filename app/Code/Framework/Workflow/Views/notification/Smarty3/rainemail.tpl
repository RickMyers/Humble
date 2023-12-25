{assign var=data value=$element->load()}
<style type="text/css">
    .argus-config-descriptor {
        font-size: .8em; font-family: serif; letter-spacing: 2px; box-sizing: border-box
    }
    .argus-config-field {
        font-size: 1em; font-family: sans-serif; text-align: right; padding-right: 4px; margin: 0px; box-sizing: border-box
    }
    .argus-config-cell {
        width: 33%; background-color: #e8e8e8;  border: 1px solid #d0d0d0; padding-left: 2px; display: inline-block; border-collapse: collapsed; margin: 0px; box-sizing: border-box
    }
    .email-add-button {
        background-color: red; font-size: 1.5em; font-weight: bold; padding: 0px 4px; border: 1px solid red; border-radius: 4px; color: white; font-weight: bold
    }
    .email-dropdown {
        width: 325px; padding: 3px 5px; border: 1px solid #aaf; border-radius: 3px;
    }
    .email-field-description {
        font-size: .85em; letter-spacing: 1px; font-family: monospace; margin-bottom: 15px
    }
    .email-input-box {
        padding: 2px; color: #333; background-color: lightcyan; border: 1px solid #333; font-size: 1em; width: 60%
    }
</style>
<div id="header-{$data.id}" style="position: relative">
    <div style="height: 30px; white-space: nowrap; overflow: hidden">
        <div class="argus-config-cell"><div class="argus-config-descriptor">Type</div><div class="argus-config-field">{$data.type }</div></div>
        <div class="argus-config-cell"><div class="argus-config-descriptor">Shape</div><div class="argus-config-field">{$data.shape }</div></div>
        <div class="argus-config-cell"><div class="argus-config-descriptor">Mongo ID</div><div class="argus-config-field">{$data.id }</div></div>
    </div>
    <div style="height: 30px; white-space: nowrap; overflow: hidden">
        <div class="argus-config-cell"><div class="argus-config-descriptor">Namespace</div><div class="argus-config-field">{if ($data.namespace)}{$data.namespace}{else}N/A{/if}</div></div>
        <div class="argus-config-cell"><div class="argus-config-descriptor">Component</div><div class="argus-config-field">{if ($data.component)}{$data.component}{else}N/A{/if}</div></div>
        <div class="argus-config-cell"><div class="argus-config-descriptor">Method</div><div class="argus-config-field">{if ($data.method)}{$data.method}{else}N/A{/if}</div></div>
    </div>
</div>
<div>
    <form onsubmit="return false" name="config-email-section" id="config-email-section-{$data.id}">
        <fieldset><legend>Instructions</legend>
            <div style="position: relative;" id="input-{$data.id}">
                <p>
                    Below you can specify whether the recipient(s) are listed in the box below, or are obtained from a field in the event.  The same is true of the subject.  The attachment field always pulls from the event, so you would list the name of the field in the event (if any) that contains the attachment location
                </p>               
                <input value="{if (isset($data.recipients))}{$data.recipients} {/if}" type="text" name="recipients" id="recipients-{$data.id}" placeholder="Recipient List..." class="email-input-box" />
                <input type="radio" name="recipients_source" id="recipients_source_value-{$data.id}" value="value" {if (isset($data.recipients_source) && ($data.recipients_source=='value'))}checked="checked"{/if}/> Value
                <input type="radio" name="recipients_source" id="recipients_source_field-{$data.id}" value="field" {if (isset($data.recipients_source) && ($data.recipients_source=='field'))}checked="checked"{/if}/> Field
                <div class="email-field-description">
                    Recipients, separated by semicolons
                </div>
                <input value="{if (isset($data.subject))}{$data.subject} {/if}" type="text" name="subject" id="subject-{$data.id}" placeholder="Subject..." class="email-input-box" />
                <input type="radio" name="subject_source" id="subject_source_value-{$data.id}" value="value" {if (isset($data.subject_source) && ($data.subject_source=='value'))}checked="checked"{/if}/> Value 
                <input type="radio" name="subject_source" id="subject_source_field-{$data.id}" value="field" {if (isset($data.subject_source) && ($data.subject_source=='field'))}checked="checked"{/if}/> Field
                <div class="email-field-description">
                    Subject
                </div>
            </div>
            <div style="width: 100%" name='email-editor' id='email-editor-{$data.id}'></div>
            <input type='button' name='email-template-save' style="float: left" id='email-template-save-{$data.id}' value=" Save "/>
            <div style="float: right; position: relative" id="attachment-{$data.id}">
                Attachment Event Field: <input value="{if (isset($data.attachment_event_field))}{$data.attachment_event_field} {/if}" type="text" name="attachment_event_field" id="attachment_event_field-{$data.id}" class="email-input-box" style="width: 200px" />
            </div>
            <textarea id="editor-template-{$data.id}" style="display: none">{if (isset($data.email_template))}{$data.email_template}{/if}</textarea>
        </fieldset>
    </form>
</div>
<form name="config-email-template-form" id="config-email-template-form-{$data.id}">
    <input type="hidden" name="email_template" id="email_template_{$data.id}" value="" />
    <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
    <input type="hidden" name="subject" id="subject_{$data.id}" value="" />
    <input type="hidden" name="subject_source" id="subject_source_{$data.id}" value="" />
    <input type="hidden" name="recipients" id="recipients_{$data.id}" value="" />
    <input type="hidden" name="recipients_source" id="recipients_source_{$data.id}" value="" />
    <input type="hidden" name="attachment_event_field" id="attachment_event_field_{$data.id}" value="" />
    <input type='hidden' name='windowId' id='windowId_{$data.id}' value='{$window_id}' />
</form>
<script type="text/javascript">
    //--------------------------------------------------------------------------
    // What we are doing here:
    // 
    // o There are two forms above... one conforms to the normal Paradigm way
    // o The other is to catch normal intercepted form data, but also interface
    //   with the Ace Editor to get the contents of the code editor and add
    //   that to the the data we send to get saved
    //--------------------------------------------------------------------------
    Form.intercept($('#config-email-template-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$window_id}");
    Paradigm.Editors['{$data.id}'] = ace.edit("email-editor-{$data.id}");
    Paradigm.Editors['{$data.id}'].setTheme("ace/theme/github");                //Configurable look and feel to the editor
    Paradigm.Editors['{$data.id}'].session.setMode("ace/mode/smarty");          //Closest language to Rain is Smarty... deal with it
    Paradigm.Editors['{$data.id}'].setValue($('#editor-template-{$data.id}').val()); //We get the data from the textarea in the normal form, and then "inject" it into the Ace Editor
    (function () {
        //Usual window resize nonsense
        var win = Desktop.window.list['{$window_id}'];
        var header = $E('header-{$data.id}');
        var input  = $E('input-{$data.id}');
        var editor = $E('email-editor-{$data.id}');
        var attach = $E('attachment-{$data.id}');
        win.resize = function () {
            var nh = win.content.height() - header.offsetHeight - attach.offsetHeight - input.offsetHeight - 65;
            $(editor).height(nh);
        }
    })();
    $('#email-template-save-{$data.id}').on('click',function () {
            function getRadioValue(form,field) {
                var cbArray = form.elements[field];
                var cbCtr   = 0;
                var val     = '';
                while ((cbCtr < cbArray.length) && (!val))	{
                    val = cbArray[cbCtr].checked ? cbArray[cbCtr].value : "";
                    cbCtr++;
                }
                return val;          
            }
        //if (Edits['email_template_form_{$data.id}'].validate()) {
            var template    = Paradigm.Editors['{$data.id}'].getValue();
            $('#email_template_{$data.id}').val(template);
            $('#subject_{$data.id}').val($('#subject-{$data.id}').val());
            $('#recipients_{$data.id}').val($('#recipients-{$data.id}').val());
            $('#subject_source_{$data.id}').val(getRadioValue($E('config-email-section-{$data.id}'),'subject_source'));
            $('#recipients_source_{$data.id}').val(getRadioValue($E('config-email-section-{$data.id}'),'recipients_source'));
            $('#attachment_event_field_{$data.id}').val($('#attachment_event_field-{$data.id}').val());
            $('#config-email-template-form-{$data.id}').submit();
        //}
    });
</script>


