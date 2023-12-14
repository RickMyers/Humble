<style type="text/css">
    .target-delete {
        height: 16px; cursor: pointer
    }
    .target-token {
        border: 1px solid #333; color: #333; background-color: lightcyan; padding: 2px; width: 300px; font-family: monospace
    }
    .token-generate {
        
    }
    .new-export {
        font-weight: bolder; font-size: 1.2em; padding: 5px
    }
    #import_export_targets_form th {
        text-align: left; font-weight: bolder;
    }
    
</style>
<form name="import_export_targets_form" id="import_export_targets_form" onsubmit="return false">
    <table style="width: 100%">
        <tr>
            <th style='text-align: center'>&diams;</th>
            <th>Export Alias</th>
            <th>URL Of System To Export To</th>
            <th>Security Token To Send</th>
        </tr>
        <tr><td colspan="4"><hr /></td></tr>
        {foreach from=$targets->fetch() item=target}
        <tr style="background-color: rgba(202,202,202,{cycle values=".1,.3"})">
            <td><img src='/images/paradigm/clipart/red-x.png' class='target-delete' token_id='{$target.id}' /></td>
            <td>{$target.alias}</td>
            <td>{$target.target}</td>
            <td><input type="text" id='target_token_{$target.id}' token_id="{$target.id}" class="target-token" value="{$target.token}" /><button token_id='{$target.id}' class='token-generate'> Generate </button></td>
        </tr>
        {/foreach}
        <tr style="background-color: rgba(202,202,202,{cycle values=".1,.3"})">
            <td><button class='new-export-button' id="new_export_button"> -|- </button></td>
            <td><input type="text" name="export_alias" id="export_alias" value=''  /></td>
            <td><input type="text" name="export_url"   id="export_url" value=''  /></td>
            <td></td>
        </tr>
        
    </table>
</form>
<script type="text/javascript">
    (function () { 
        new EasyEdits('/edits/paradigm/targets','export-targets');
        $('.target-token').on('change',function (evt) {
            let token_id = evt.target.getAttribute('token_id');
            (new EasyAjax('/paradigm/workflow/updatetarget')).add('token_id',token_id).add('token',genToken()).then((response) => {
                $('#paradigm_manage_targets').html(response);
            }).post();
        });
        $('.target-delete').on('click',function (evt) {
            let token_id = evt.target.getAttribute('token_id');
            if (confirm('Delete that target?')) {
                (new EasyAjax('/paradigm/workflow/deletetarget')).add('token_id',token_id).then((response) => {
                    $('#paradigm_manage_targets').html(response);
                }).post();
            }
            
        });
        $('.token_generate').on('click',function (evt) {
            let token_id = evt.target.getAttribute('token_id');
            function genToken() {
                let tokens = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz_&^%$#@!-_';
                let token  = '';
                for (let i=0; i<32; i++) {
                    token += ''+tokens.substr(Math.floor(Math.random() * tokens.length),1);
                }
                return token;
            }
            $('#target_token_'+token_id).val(genToken).trigger('change');
        });
    })();
</script>