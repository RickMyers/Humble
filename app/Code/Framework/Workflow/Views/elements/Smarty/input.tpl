<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px; padding-bottom: 10px
    }
</style>

<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>
            <h1>{$window_id$}</h1>
            <form name='io-form' id='humble-paradigm-config-io-form-{$manager->getId()}' onsubmit='return false'>
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
                <div>
                    <div>
                        <select name='namespace' id='humble-paradigm-config-io-form-namespace-{$manager->getId()}'>
                            <option value=''>Please choose from this list</option>
                        </select>
                    </div>
                    <div class='form-field-description'>Available Object Collections</div>
                    <div>
                    <select name='component' id='humble-paradigm-config-io-form-component-{$manager->getId()}'>
                        <option value=''>Please choose from this list</option>
                    </select>
                    </div>
                    <div class='form-field-description'>Available Process Objects</div>
                    <div style='white-space: nowrap'>
                        <select name='method' id='humble-paradigm-config-io-form-method-{$manager->getId()}'>
                            <option value=''>Please choose from this list</option>
                        </select>
                    </div>
                    <div class='form-field-description'>Available Process Methods</div>
                </div>
                <input type='button' name='save' id='humble-paradigm-config-io-form-save-{$manager->getId()}' style='display: inline-block' />
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
/*    var ee = new EasyEdits(null,'process_{$manager->getId()}');
    ee.fetch('/edits/workflow/io');
    ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
    Form.intercept($('#humble-paradigm-config-io-form-{$manager->getId()}').get(),'{$manager->getId()}');
    $('#view_code-{$manager->getId()}').on('click',(evt)=>{
        let win = Desktop.semaphore.checkout(true);
        (new EasyAjax('/workflow/elements/explore')).add('window_id',win.id).packageForm('humble-paradigm-config-io-form-{$manager->getId()}').then((response) => {
            win._title('Workflow Explore')._scroll(true)._open(response);
        }).post();
    });*/
</script>