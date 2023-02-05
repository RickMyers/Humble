<style type="text/css">
    .source-delete {
        height: 16px; cursor: pointer
    }
    .source-token {
        border: 1px solid #333; color: #333; background-color: lightcyan; padding: 2px
    }
    #import-export-sources-form th {
        text-align: left; font-weight: bolder;
    }
</style>
<form name="import_export_sources_form" id="import_export_sources_form" onsubmit="return false">
    <table style="width: 100%">
        <tr>
            <th style='text-align: center'>&diams;</th>
            <th>Import Alias</th>
            <th>URL To Import From</th>
            <th>Security Token To Receive</th>
        </tr>
        <tr><td colspan="4"><hr /></td></tr>
        {foreach from=$sources->fetch() item=source}
        <tr style="background-color: rgba(202,202,202,{cycle values=".1,.3"})">
            <td><img src='/images/paradigm/clipart/red-x.png' class='source-delete' token_id='{$source.id}' /></td>
            <td>{$source.name}</td>
            <td>{$source.source}</td>
            <td><input type="text" token_id="{$source.id}" class="source-token" value="{$source.token}" /></td>
        </tr>
        {/foreach}
        <tr style="background-color: rgba(202,202,202,{cycle values=".1,.3"})">
            <td><button class='new-import' id="new_import_button"> -|- </button></td>
            <td><input type="text" name="import_alias" id="import_alias" value=''  /></td>
            <td><input type="text" name="import_url"   id="import_url" value=''  /></td>
            <td><input type="text" name="import_token" id="import_token" value=''  /></td>
        </tr>
        
    </table>
</form>
<script type="text/javascript">
    (function () { 
        new EasyEdits('/edits/paradigm/sources','import-sources');
        $('.source-token').on('change',function (evt) {
            let token_id = evt.target.getAttribute('token_id');
            alert(token_id);
            if (confirm('Update Security Token For That Source?')) {
                
            }
        });
        $('.source-delete').on('click',function (evt) {
            let token_id = evt.target.getAttribute('token_id');
            if (confirm('Delete That Import Source?')) {
                
            }
            alert(token_id);
        });        
    })();
</script>
    