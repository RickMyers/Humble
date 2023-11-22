<div id="paradigm-sync-header-{$windowId}" style="background-color: #333; color: white; font-size: .9em; padding: 5px; box-sizing: border-box">
</div>
<div id="paradigm-sync-content-{$windowId}">
<table style="width: 100%; height: 100%">
    <tr>
        <td align="center">
            <form name="sync-form" id="sync-form-{$windowId}" onsubmit="return false">
                <div style="width: 300px; margin-right: auto; margin-left: auto; overflow: hidden; white-space: nowrap; font-size: 2.0em; font-family: sans-serif">
                    Sync Workflows
                </div>
                <br /><br /><img src="/images/paradigm/clipart/sync.png" style="width: 250px;" id='sync-image' /><br /><br />
                <div style="width: 300px; margin-right: auto; margin-left: auto; text-align: left; overflow: hidden; white-space: nowrap">
                    <select name="target" id="sync-target-{$windowId}" style="width: 100%; padding: 2px; border: 1px solid #aaf; background-color: #FFEBC9">
                        <option style="font-style: italic" value="">Choose destination server...</option>
                        {foreach from=$sources->fetch() item=option}
                            <option value="{$option.id}">{$option.name} - {$option.source}</option>
                        {/foreach}
                    </select><br /><br />
                    <input type="button" style="background-color: #0077AF; font-size: 1.1em; padding: 2px 5px; color: white" id="paradigm-sync-submit-{$windowId}" name="paradigm-sync-submit" value="   Sync   " />
                </div>
            </form>
        </td>
    </tr>
</table>
</div>
<div id="paradigm-sync-footer-{$windowId}" style="background-color: #333; color: white; font-size: .9em; padding: 5px; box-sizing: border-box">
    &copy; 2015-present, Humble Project
</div>
<script type="text/javascript">
    Desktop.window.list['{$windowId}'].resize = (function ($) {
        return function() {
            $('#paradigm-sync-content-{$windowId}').height(this.content.offsetHeight - $E('paradigm-sync-header-{$windowId}').offsetHeight - $E('paradigm-sync-footer-{$windowId}').offsetHeight);
        }
    })($);
    $('#paradigm-sync-submit-{$windowId}').on('click',function () {
        if ($('#sync-target-{$windowId}').val()) {
            Paradigm.actions.animate.run('sync-image');
            $('#paradigm-sync-submit-{$windowId}').attr('disabled','true');
            (new EasyAjax('/paradigm/workflow/sync')).add('windowId','{$windowId}').add('destination_id',$('#sync-target-{$windowId}').val()).then((response) => {
                Paradigm.actions.animate.stop();
                $('#paradigm-sync-submit-{$windowId}').attr('disabled','false');
                alert((response) ? response : "Synced, I think...");
                Desktop.window.list['{$windowId}']._close();
            }).post();
        } else {
            alert('Please choose a destination for the sync');
        }
    });
</script>
