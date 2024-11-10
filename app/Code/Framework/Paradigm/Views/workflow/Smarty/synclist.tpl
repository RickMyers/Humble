<div id="paradigm-sync-header-{$window_id}" style="background-color: #333; color: white; font-size: .9em; padding: 5px; box-sizing: border-box">
</div>
<div id="paradigm-sync-content-{$window_id}">
<table style="width: 100%; height: 100%">
    <tr>
        <td align="center">
            <form name="sync-form" id="sync-form-{$window_id}" onsubmit="return false">
                <div style="width: 300px; margin-right: auto; margin-left: auto; overflow: hidden; white-space: nowrap; font-size: 2.0em; font-family: sans-serif">
                    Sync Workflows
                </div>
                <br /><br /><img src="/images/paradigm/clipart/sync.png" style="width: 250px;" id='sync-image' /><br /><br />
                <div style="width: 300px; margin-right: auto; margin-left: auto; text-align: left; overflow: hidden; white-space: nowrap">
                    <select name="target" id="sync-target-{$window_id}" style="width: 100%; padding: 2px; border: 1px solid #aaf; background-color: #FFEBC9">
                        <option style="font-style: italic" value="">Choose destination server...</option>
                        {foreach from=$sources->fetch() item=option}
                            <option value="{$option.id}">{$option.name} - {$option.source}</option>
                        {/foreach}
                    </select><br /><br />
                    <input type="button" style="background-color: #0077AF; font-size: 1.1em; padding: 2px 5px; color: white" id="paradigm-sync-submit-{$window_id}" name="paradigm-sync-submit" value="   Sync   " />
                </div>
            </form>
        </td>
    </tr>
</table>
</div>
<div id="paradigm-sync-footer-{$window_id}" style="background-color: #333; color: white; font-size: .9em; padding: 5px; box-sizing: border-box">
    &copy; 2015-present, Humble Project
</div>
<script type="text/javascript">
    Desktop.window.list['{$window_id}'].resize = (function ($) {
        return function() {
            $('#paradigm-sync-content-{$window_id}').height(this.content.offsetHeight - $E('paradigm-sync-header-{$window_id}').offsetHeight - $E('paradigm-sync-footer-{$window_id}').offsetHeight);
        }
    })($);
    $('#paradigm-sync-submit-{$window_id}').on('click',function () {
        if ($('#sync-target-{$window_id}').val()) {
            Paradigm.actions.animate.run('sync-image');
            $('#paradigm-sync-submit-{$window_id}').attr('disabled','true');
            (new EasyAjax('/paradigm/workflow/sync')).add('window_id','{$window_id}').add('destination_id',$('#sync-target-{$window_id}').val()).then((response) => {
                Paradigm.actions.animate.stop();
                $('#paradigm-sync-submit-{$window_id}').attr('disabled','false');
                alert((response) ? response : "Synced, I think...");
                Desktop.window.list['{$window_id}']._close();
            }).post();
        } else {
            alert('Please choose a destination for the sync');
        }
    });
</script>
