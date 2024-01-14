{assign var=data value=$element->load()}
<style type="text/css">
    .paradigm-config-descriptor {
        font-size: .8em; font-family: serif; letter-spacing: 2px;
    }
    .paradigm-config-field {
        font-size: 1em; font-family: sans-serif; text-align: right; padding-right: 4px;
    }
    .paradigm-config-cell {
        width: 33%; margin: 1px; background-color: #e8e8e8;  border: 1px solid #d0d0d0; padding-left: 2px
    }
</style>
<table style="width: 100%; height: 100%; border-spacing: 1px;">
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">{$data.type}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">{$data.shape}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$data.id}</div></td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            
            <form name="config-begin-form" id="config-begin-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="windowId" id="windowId_{$data.id}" value="{$windowId}" />
                <fieldset style="padding: 10px; width: 600px; text-align: left"><legend>Instructions</legend>
                    This is the beginning of a workflow, identified by label (which is a MongoDB ID) below:<br />
                    <h3 style="text-align: center; font-family: monospace; letter-spacing: 2px"><a href="#" id="test_link_for_{$data.id}" onclick="return false">{$data.id}</a></h3>

                    <textarea style="width: 100%; background-color: lightcyan; height: 100px" placeholder="Additional Workflow Information (optional)" name='description' id='description_{$data.id}'>{if (isset($data.description))}{$data.description}{/if}</textarea><br />

                    <br /><input type="submit" value=" Save " />
                </fieldset>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#config-begin-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$windowId}");
    $('#test_link_for_{$data.id}').on("click",function (evt) {
        if (confirm('Would you like to run this workflow?')) {
            (new EasyAjax('/paradigm/workflow/run')).add('workflow_id','{$data.id}').then((response) => {
                alert('Done, output is in the console.');
                console.log(response);
            }).post();
        }
    });
</script>