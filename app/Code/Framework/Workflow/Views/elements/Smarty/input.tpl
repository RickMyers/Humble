<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px; padding-bottom: 10px
    }
</style>

<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle' style='font-size: 1.2em'>
            <form name='io-form' id='paradigm-config-io-form-{$manager->getId()}' onsubmit='return false'>
            <input type="hidden" name="window_id" id="window-id-{$manager->getId()}" value="{$manager->getWindowId()}" />
            <input type="hidden" name="id" id="element-id-{$manager->getId()}" value="{$manager->getId()}" />
            <input type="hidden" name="component" id="component-{$manager->getId()}" value="I/O" />
            <input type="hidden" name="method" id="method-{$manager->getId()}" value="" />
            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                Initial Component Configuration
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px; font-size: 1.2em'>
                Initial element configuration.  To begin configuring this I/O element, choose whether
                you want to Input a file, or reference to a file, to the event, or whether this is an
                Output, in which case you can write a field or the entire event to a file
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px'>
                <img src='/images/paradigm/clipart/manual_input.png' style='float: right' />
                <div>
                    <div style='white-space: nowrap'>
                        <select name='type' id='paradigm-config-io-form-type-{$manager->getId()}'>
                            <option value=''></option>
                            <option value='Input'> File Input (add) To Event</option>
                            <option value='Output'> Event Output (write) To File</option>
                        </select>
                    </div>
                </div>
                <div class='form-field-description'>
                    I/O Selection
                </div>
                <input type='button' name='save' id='paradigm-config-io-form-save-{$manager->getId()}' style='display: inline-block' />
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    var ee = new EasyEdits(null,'io_{$manager->getId()}');
    ee.fetch('/edits/workflow/io');
    ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
    Form.intercept($('#paradigm-config-io-form-{$manager->getId()}').get(),'{$manager->getId()}');
</script>