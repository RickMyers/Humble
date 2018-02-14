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
        background-color: red; font-size: 1.5em; font-weight: bold; padding: 0px 4px; border: 1px solid red; border-radius: 4px; color: white; font-weight: bold
    }
    .email-dropdown {
        width: 325px; padding: 3px 5px; border: 1px solid #aaf; border-radius: 3px;
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

                <div style="min-width: 500px; max-width: 720px; width: 70%; padding: 20px; position: relative; background-color: #0044cc; color: white; text-align: left;">
                    <div style="font-size: 2em; font-family: sans-serif;">Send An Email</div>
                    <hr />
                    <form name="email-management-form" id="email-management-form-{$data.id}" style="position: relative" onsubmit="return false">
                        <input type='hidden' name='email-template-text' id='email-template-text-{$data.id}' value='' />
                        <select class="email-dropdown" name="email-category" id="email-category-{$data.id}">
                            <option value="">Choose...</option>
                            {foreach from=$categories->fetch() item=category}
                                <option value="{$category.id}">{$category.category}</option>
                            {/foreach}
                        </select><br />
                        <div class="email-dropdown-description">Category</div>
                        <select class="email-dropdown" name="email-template" id="email-template-{$data.id}" style="position: relative">
                            <option value=""> </option>
                        </select><br />
                        <div class="email-dropdown-description">Email Template</div>
                        <div>
                            <a href="#" style="color: white" onclick="$('#new-email-template-area-{$data.id}').slideToggle(); return false">New Template</a>
                        </div>
                        <div id="new-email-template-area-{$data.id}" style="display: none; padding: 20px 0px 10px 0px" >
                            <input type="text" name="new-email-template" id="new-email-template-{$data.id}" /><br />
                            <div class="email-dropdown-description">New Email Description</div>
                        </div>
                        <textarea name='email-editor' id='email-editor-{$data.id}'>

                        </textarea><br /><br />
                        <input type="button" name='set-email-template' id='set-email-template-{$data.id}' style='float: right' value=' Set Email ' classname='settingsButton' />
                        <input type='button' name='email-template-save' id='email-template-save-{$data.id}' />
                    </form>
                </div>
            </td>
        </tr>

</table>

<form name="config-email-template-form" id="config-email-template-form-{$data.id}">
    <input type="hidden" name="email_template" id="email_template_{$data.id}" value="" />
    <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
    <input type="hidden" name="email_category" id="email_category_{$data.id}" value="" />
    <input type="hidden" name="email_description" id="email_description_{$data.id}" value="" />
    <input type='hidden' name='windowId' id='windowId_{$data.id}' value='{$helper->getWindowId()}' />
</form>
<script type="text/javascript">
    alert('here')
    var ee = new EasyEdits(null,'email_template_form_{$data.id}');
    ee.fetch('/edits/workflow/email');
    ee.process(ee.getJSON().replace(/&id&/g,'{$data.id}').replace(/&window_id&/g,'{$helper->getWindowId()}'));
    Form.intercept($('#email-management-form-{$data.id}').get(),'{$data.id}');
    CKEDITOR.replace('email-editor-{$data.id}');
    Form.intercept($('#config-email-template-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$helper->getWindowId()}");
    $('#set-email-template-{$data.id}').on('click',function () {
        if (Edits['email_template_form_{$data.id}'].validate()) {
            var template    = CKEDITOR.instances['email-editor-{$data.id}'].getData();
            var category = $('#email-category-{$data.id} option:selected').text();
            var description = $('#new-email-template-{$data.id}').val() || $('#email-template-{$data.id} option:selected').text();
            $('#email_template_{$data.id}').val(template);
            $('#email_category_{$data.id}').val(category);
            $('#email_description_{$data.id}').val(description);
            $('#config-email-template-form-{$data.id}').submit();
        }
    });
</script>
