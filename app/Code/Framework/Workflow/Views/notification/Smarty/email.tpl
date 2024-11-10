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
    .email-add-button {
        background-color: red; font-size: 1.5em; font-weight: bold; padding: 0px 4px; border: 1px solid red; border-radius: 4px;  font-weight: bold
    }
    .email-field {
        width: 80%; background-color: lightcyan; padding: 2px; color: #333; border-radius: 2px; border: 1px solid silver
    }
    .email-field-desc {
        padding-bottom: 15px; font-weight: bolder
    }
    .email-dropdown-description {
        font-family: sans-serif; letter-spacing: 3px; color: white; padding-bottom: 15px;
    }
</style>
<table style="width: 100%; height: 100%">
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">{$data.type }</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">{$data.shape }</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$data.id }</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{if ($data.namespace)}{$data.namespace}{else}N/A{/if}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">{if ($data.component)}{$data.component}{else}N/A{/if}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">{if ($data.method)}{$data.method}{else}N/A{/if}</div></td>
    </tr>
        <tr>

            <td colspan=3 align='center' valign='middle'>

                <div style="min-width: 500px; width: 900px;  position: relative; color: #333; text-align: left;">

                    <form name="email-management-form" id="email-management-form-{$data.id}" style="position: relative" onsubmit="return false">
                        <fieldset style='background-color: #dedede; padding: 10px; border-radius: 10px; border: 1px solid silver'><legend style='font-weight: bolder'>Instructions</legend>
                            You can generate an e-mail by specifying the data below, or by specifying the field in the event that has the data you wish to use to populate the fields below.
                            If you will be pulling data from the event to populate the fields below, then choose "Field" from below, and the value you enter should be the name of the field on the event.
                            Otherwise if you are directly setting the values for the fields below, choose "Value" from below:<br /><br />
                            <input type='hidden' name='window_id' id='window_id-{$window_id}' value='{$window_id}' />
                            <input type='hidden' name='id' id='id-{$window_id}' value='{$data.id}' />
                            <input type='hidden' name='email_message' id='email_message-{$data.id}' value='' />
                            <div>
                                <input type="text" value="{if (isset($data.from) && ($data.from))}{$data.from}{/if}" class="email-field" name="from" id="email-from-{$window_id}" />
                                <input type="radio" name="from_type" id="from_type_value-{$window_id}" value="field" {if (isset($data.from_type) && ($data.from_type=='field'))}checked{/if} /> Field
                                <input type="radio" name="from_type" id="from_type_field-{$window_id}" value="value" {if (isset($data.from_type) && ($data.from_type=='value'))}checked{/if}/> Value
                            </div>
                            <div class="email-field-desc">From Party</div>
                            <div>
                                <input type="text" value="{if (isset($data.recipients) && ($data.recipients))}{$data.recipients}{/if}" class="email-field" name="recipients" id="email-recipients-{$window_id}" />
                                <input type="radio" name="recipient_type" id="recipient_type_value-{$window_id}" value="field" {if (isset($data.recipient_type) && ($data.recipient_type=='field'))}checked{/if} /> Field
                                <input type="radio" name="recipient_type" id="recipient_type_field-{$window_id}" value="value" {if (isset($data.recipient_type) && ($data.recipient_type=='value'))}checked{/if}/> Value
                            </div>
                            <div class="email-field-desc">Recipient(s)</div>
                            <div>
                                <input type="text" value="{if (isset($data.subject) && ($data.subject))}{$data.subject}{/if}" class="email-field" name="subject" id="email-subject-{$window_id}" />
                                <input type="radio" name="subject_type" id="subject_type_value-{$window_id}" value="field" {if (isset($data.subject_type) && ($data.subject_type=='field'))}checked{/if} /> Field
                                <input type="radio" name="subject_type" id="subject_type_field-{$window_id}" value="value" {if (isset($data.subject_type) && ($data.subject_type=='value'))}checked{/if}/> Value
                            </div>
                            <div class="email-field-desc">Subject</div>
                            <div>
                                <input type="text" value="{if (isset($data.message_field) && ($data.message_field))}{$data.message_field}{/if}" class="email-field" name="message_field" id="message_field-{$window_id}" />
                            </div>
                            <div class="email-field-desc">Message Field Name (set this or the message text below)</div>
                            <textarea name='email-editor' id='email-editor-{$data.id}'>{if (isset($data.email_message) && ($data.email_message))}{$data.email_message}{/if}</textarea><br /><br />
                            <input type="button" name='set-email-template' id='set-email-template-{$data.id}' style='float: right' value=' Save ' />
                        </fieldset>
                    </form>
                </div>
            </td>
        </tr>

</table>

<script type="text/javascript">
    $('#set-email-template-{$data.id}').on('click',(function (cke) {
        return function () {
            if (!$('#message_field-{$window_id}').val()) {
                $('#email_message-{$data.id}').val(cke.getData());
            }
            $('#email-management-form-{$data.id}').submit();
        }})(CKEDITOR.replace('email-editor-{$data.id}'))
    );
    Form.intercept($('#email-management-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$window_id}");
    Desktop.window.list['{$window_id}']._scroll(true);
</script>
