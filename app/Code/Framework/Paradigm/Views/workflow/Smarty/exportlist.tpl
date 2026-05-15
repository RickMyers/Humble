<table style="width: 100%; height: 100%">
    <tr>
        <td style="background-color: #333; color: white; font-size: .9em; padding: 5px; box-sizing: border-box">
            Workflow Export
        </td>
    </tr>
    <tr>
        <td>
            <form name="export-form" id="export-form-{$window_id}" onsubmit="return false">
                <div style="width: 300px; margin-right: auto; margin-left: auto; overflow: hidden; white-space: nowrap; font-size: 2.0em; font-family: sans-serif">
                    Export Current Workflow
                </div>
                <center>
                <img src="/images/paradigm/clipart/export.png" style="width: 450px; margin-left: auto; margin-right: auto; margin-bottom: 30px; margin-top: 30px" />
                </center>
                <div style="width: 300px; margin-right: auto; margin-left: auto; text-align: left; overflow: hidden; white-space: nowrap">
                    <select name="target" id="export-target-{$window_id}" style="width: 100%; padding: 2px; border: 1px solid #aaf; background-color: #FFEBC9">
                        <option style="font-style: italic" value="">Choose destination server...</option>
                        {foreach from=$targets->fetch() item=option}
                            <option value="{$option.id}">{$option.alias} - {$option.target}</option>
                        {/foreach}
                        <option value='file'> File... </option>
                    </select><br /><br />
                    <input type="button" style="background-color: #0077AF; font-size: 1.1em; padding: 2px 5px; color: white" id="paradigm-export-submit-{$window_id}" name="paradigm-export-submit" value="  Export  " />
                </div>
            </form>
            <form name='file-export-form' id='file-export-form' action='/paradigm/workflow/export' method='POST'>
                <input type='hidden' name='id' id='id' value='{$id}' />
                <input type='hidden' name='destination_id' id='destination_id' value='file' />
                <input type='hidden' name='file' id='file' value='' />
            </form>
        </td>
    </tr>
    <tr>
        <td style="background-color: #333; color: white; font-size: .9em; padding: 5px; box-sizing: border-box; text-align: right; padding-right: 15px">
    &copy; 2015-present, Humble Project
        </td>
    </tr>
            </table>
<script type="text/javascript">
    $('#paradigm-export-submit-{$window_id}').on('click',function () {
        if ($('#export-target-{$window_id}').val()) {
            if ($('#export-target-{$window_id}').val()==='file') {
                let file = prompt('Please Enter A File Name');
                if (file) {
                    $('#file').val(file);
                    $E('file-export-form').submit();
                }
            } else {
                (new EasyAjax('/paradigm/workflow/export')).add('file',file).add('id','{$id}').add('window_id','{$window_id}').add('destination_id',$('#export-target-{$window_id}').val()).then((response) => {
                    alert((response) ? response : "Exported, I think...");
                    Desktop.window.list['{$window_id}']._close();
                }).post();
            }
        } else {
            alert('Please choose a destination for the export');
        }
    });
</script>
