<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px
    }
</style>
<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>
            <form name='humble-paradigm-config-notification-form' id='humble-paradigm-config-notification-form-{$manager->getId()}' onsubmit='return false'>
            <input type="hidden" name="windowId" id="window-id-{$manager->getId()}" value="{$manager->getWindowId()}" />
            <input type="hidden" name="id" id="element-id-{$manager->getId()}" value="{$manager->getId()}" />
            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                Initial Component Configuration
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                Initial element configuration.  To begin configuring this notification element, please choose the appropriate
                container object below, and then choose what action you'd like this process to perform.  Afterwards a detailed
                configuration panel will appear if applicable
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px'>
                <img src='/images/paradigm/clipart/notification.png' style='float: right; height: 110px' />
                <select name='namespace' id='humble-paradigm-config-notification-form-namespace-{$manager->getId()}'>
                    <option value=''>Please choose from this list</option>
                    {foreach from=$modules->fetch() item=module}
                        <option value='{$module.namespace}'>{$module.namespace|ucfirst}</option>
                    {/foreach}
                    
                </select>
                <div class='form-field-description'>Available Object Collections</div>
                <br />
                <select name='component' id='humble-paradigm-config-notification-form-component-{$manager->getId()}'>
                    <option value=''>Please choose from this list</option>
                </select>
                <div class='form-field-description'>Available Process Objects</div><br />
                <select name='method' id='humble-paradigm-config-notification-form-method-{$manager->getId()}'>
                    <option value=''>Please choose from this list</option>
                </select>
                <div class='form-field-description'>Available Process Methods</div>
                <br />
                <input type='button' name='notification-form-save' id='humble-paradigm-config-notification-form-save-{$manager->getId()}' />
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    var ee = new EasyEdits(null,'notification_{$manager->getId()}');
    ee.fetch('/edits/workflow/notification');
    ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
    Form.intercept($('#humble-paradigm-config-notification-form-{$manager->getId()}').get(),'{$manager->getId()}')
</script>