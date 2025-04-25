{assign var=data value=$element->load()}
{assign var=id   value=$manager->getId()}
<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px; padding-bottom: 10px
    }
</style>

<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle' style='font-size: 1.2em'>
            <form name='io-form' id='paradigm-config-io-form-{$id}' onsubmit='return false'>
            <input type="hidden" name="window_id" id="window-id-{$id}" value="{$manager->getWindowId()}" />
            <input type="hidden" name="id" id="element-id-{$id}" value="{$id}" />
            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                Initial Component Configuration
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px; font-size: 1.2em'>
                <img src='/images/paradigm/clipart/manual_input.png' style='float: right' />
                Initial element configuration.  To begin configuring this I/O element, choose whether
                you want to Input a file, or reference to a file, to the event, or whether this is an
                Output, in which case you can write a field or the entire event to a file
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; padding-top: 15px'>
                <div id="input_tab_{$id}">
                    <br /><input type="radio" name="io_type" id="io_input_{$id}" value="input"> Add files to event <input type="radio" name="io_type" id="io_output_{$id}" value="output"> Write fields from event <br /><br />
                    <div>
                        <input style="width: 350px; padding: 2px 4px; border-radius: 2px" type="text" name="io_field" id="io_field_{$id}"/>
                    </div>
                    <div class='form-field-description'>
                        Event Field (if writing, can use '*' to write all fields on event)
                    </div>
                    
                    <div>
                        <input style="width: 350px; padding: 2px 4px; border-radius: 2px" type="text" name="io_directory" id="io_directory_{$id}"/>
                    </div>
                    <div class='form-field-description'>
                        Directory
                    </div>
                    <div>
                        <input style="width: 350px; padding: 2px 4px; border-radius: 2px" type="text" name="io_file" id="io_file_{$id}"/>
                    </div>
                    <div class='form-field-description'>
                        File (if reading, can use '*' to add all files in directory)
                    </div>
                    <div>
                        <input type="radio" name="file_attach_type" id="link_file_{$id}" value="link" /> Link File (Recommended) <input type="radio" name="file_attach_type" id="attach_file_{$id}" value="attach" /> Attach File (<i>Danger!</i>)
                    </div>
                </div>
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; padding-top: 15px'>
                <input type='button' name='io_save_button' value="  Save  " id='paradigm-config-io-form-save-{$id}' style='display: inline-block' />
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    (() => {
        {if (isset($data.io_field))}$('#io_field_{$id}').val('{$data.io_field}');{/if}
        {if (isset($data.io_directory))}$('#io_directory_{$id}').val('{$data.io_directory}');{/if}
            {if (isset($data.io_file))}$('#io_file_{$id}').val('{$data.io_file}');{/if}
        {if (isset($data.io_type))}
            {if ($data.io_type=="input")}$('#io_input_{$id}').attr('checked','checked');{/if}
            {if ($data.io_type=="output")}$('#io_output_{$id}').attr('checked','checked');{/if}    
        {/if}
        {if (isset($data.file_attach_type))}
            {if ($data.file_attach_type=="link")}$('#link_file_{$id}').attr('checked','checked');{/if}
            {if ($data.file_attach_type=="attach")}$('#attach_file_{$id}').attr('checked','checked');{/if}    
        {/if}            

        var ee = new EasyEdits(null,'io_{$id}');
        ee.fetch('/edits/workflow/io');
        ee.process(ee.getJSON().replace(/&id&/g,'{$id}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
        //formRef,id,URL,window_id,callback,preprocess,postprocess
        var f = () => {
            var g = () => {
                Desktop.window.list['{$window_id}']._close();
            }
            window.setTimeout(g,750);
        }
        Form.intercept($('#paradigm-config-io-form-{$id}').get(),'{$id}','/workflow/elements/save',false ,false,f);
    })();
</script>