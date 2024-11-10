<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px
    }
</style>

<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>
            <form name='process-form' id='humble-paradigm-config-process-form-{$manager->getId()}' onsubmit='return false'>
            <input type="hidden" name="window_id" id="window-id-{$manager->getId()}" value="{$manager->getWindowId()}" />
            <input type="hidden" name="id" id="element-id-{$manager->getId()}" value="{$manager->getId()}" />
            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                Initial Component Configuration
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                Initial element configuration.  To begin configuring this process element, please choose the appropriate
                container object below, and then choose what action you'd like this process to perform.  Afterwards a detailed
                configuration panel will appear if applicable
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px'>
                <img src='/images/paradigm/clipart/process.png' style='float: right' />
                <select name='namespace' id='humble-paradigm-config-process-form-namespace-{$manager->getId()}'>
                    <option value=''>Please choose from this list</option>
                    {foreach from=$modules->fetch() item="module"}
                        <option value='{$module.namespace}'>{$module.namespace|ucfirst}</option>
                    {/foreach}
                </select>
                <div class='form-field-description'>Available Object Collections</div>
                <br />
                <select name='component' id='humble-paradigm-config-process-form-component-{$manager->getId()}'>
                    <option value=''>Please choose from this list</option>
                </select>
                <div class='form-field-description'>Available Process Objects</div><br />
                <select name='method' id='humble-paradigm-config-process-form-method-{$manager->getId()}'>
                    <option value=''>Please choose from this list</option>
                </select>
                <div class='form-field-description'>Available Process Methods</div>
                <br />
                <div style='float: right; display: none; width: 470px; border: 1px solid #aaf; padding: 5px 10px; background-color: #F0F0D0; border-radius: 10px ' id='config-component-comment-{$manager->getId()}'></div>
                <input type='button' name='save' id='humble-paradigm-config-process-form-save-{$manager->getId()}' />
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    var ee = new EasyEdits(null,'process_{$manager->getId()}');
    ee.fetch('/edits/workflow/process');
    ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
    Form.intercept($('#humble-paradigm-config-process-form-{$manager->getId()}').get(),'{$manager->getId()}')
</script>