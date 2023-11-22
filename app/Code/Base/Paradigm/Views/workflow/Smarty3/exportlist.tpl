<div id="paradigm-export-header-{$windowId}" style="background-color: #333; color: white; font-size: .9em; padding: 5px; box-sizing: border-box">
</div>
<div id="paradigm-export-content-{$windowId}">
<table style="width: 100%; height: 100%">
    <tr>
        <td align="center">
            <form name="export-form" id="export-form-{$windowId}" onsubmit="return false">
                <div style="width: 300px; margin-right: auto; margin-left: auto; overflow: hidden; white-space: nowrap; font-size: 2.0em; font-family: sans-serif">
                    Export Current Workflow
                </div>
                <br /><br /><img src="/images/paradigm/clipart/export.png" style="width: 450px;" /><br /><br />
                <div style="width: 300px; margin-right: auto; margin-left: auto; text-align: left; overflow: hidden; white-space: nowrap">
                    <select name="target" id="export-target-{$windowId}" style="width: 100%; padding: 2px; border: 1px solid #aaf; background-color: #FFEBC9">
                        <option style="font-style: italic" value="">Choose destination server...</option>
                        {foreach from=$targets->fetch() item=option}
                            <option value="{$option.id}">{$option.alias} - {$option.target}</option>
                        {/foreach}
                        <option value='file'> File... </option>
                    </select><br /><br />
                    <input type="button" style="background-color: #0077AF; font-size: 1.1em; padding: 2px 5px; color: white" id="paradigm-export-submit-{$windowId}" name="paradigm-export-submit" value="  Export  " />
                </div>
            </form>
                <form name='file-export-form' id='file-export-form' action='/paradigm/workflow/export' method='POST'>
                    <input type='hidden' name='id' id='id' value='{$id}' />
                    <input type='hidden' name='destination_id' id='destination_id' value='file' />
                    <input type='hidden' name='file' id='file' value='' />
                </form>
        </td>
    </tr>
</table>
</div>
<div id="paradigm-export-footer-{$windowId}" style="background-color: #333; color: white; font-size: .9em; padding: 5px; box-sizing: border-box">
    &copy; 2015-present, Humble Project
</div>
<script type="text/javascript">
    Desktop.window.list['{$windowId}'].resize = (function ($) {
        return function() {
            $('#paradigm-export-content-{$windowId}').height(this.content.offsetHeight - $E('paradigm-export-header-{$windowId}').offsetHeight - $E('paradigm-export-footer-{$windowId}').offsetHeight);
        }
    })($);
    $('#paradigm-export-submit-{$windowId}').on('click',function () {
        if ($('#export-target-{$windowId}').val()) {
            if ($('#export-target-{$windowId}').val()==='file') {
                let file = prompt('Please Enter A File Name');
                if (file) {
                    $('#file').val(file);
                    $E('file-export-form').submit();
                }
            } else {
                (new EasyAjax('/paradigm/workflow/export')).add('file',file).add('id','{$id}').add('windowId','{$windowId}').add('destination_id',$('#export-target-{$windowId}').val()).then((response) => {
                    alert((response) ? response : "Exported, I think...");
                    Desktop.window.list['{$windowId}']._close();
                }).post();
            }
        } else {
            alert('Please choose a destination for the export');
        }
    });
</script>
