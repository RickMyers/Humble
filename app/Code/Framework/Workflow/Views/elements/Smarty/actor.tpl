<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px
    }
</style>
{assign var=id        value=$manager->getId()}
{assign var=window_id value=$manager->getWindowId()}
<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>
            <form name='id' id='config-actor-form' onsubmit='return false'>
                <input type="hidden" name="window_id"   value="{$window_id}" />
                <input type="hidden" name="id"          value="{$id}" />
                <input type="hidden" name="workflow_id" value="" />
                <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                    Initial Component Configuration
                </div>
                <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                    Initial actor configuration.  To begin configuring this process element, please choose the appropriate
                    actor and event object below, and then choose what action this actor has performed.  Afterwards a detailed
                    configuration panel will appear if applicable
                </div>
                <div style='margin-left: auto; margin-right: auto; width: 545px'>
                    <img src='/images/paradigm/clipart/person1.png' style='float: right; height: 100px' />
                    <select name='namespace'>
                        <option value=''>Choose...</option>
                        <option value='{$client->getNamespace()}'>{$client->getNamespace()} - {$client->getDescription()}</option>
                    </select>
                    <div class='form-field-description'>Workflow Owner</div>
                    <br />
                    <select name='component'>
                        <option value=''>Please choose from this list</option>
                    </select>
                    <div class='form-field-description'>Available Object Collections</div>
                    <br />

                    <select name='method'>
                        <option value=''>Please choose from this list</option>
                    </select>
                    <div class='form-field-description'>Available Process Methods</div>
                    <br />
                    <div style='float: right; display: none; width: 470px; border: 1px solid #aaf; padding: 5px 10px; background-color: #F0F0D0; border-radius: 10px ' id='config-component-comment-{$id}'></div>
                    <input type='button' name='save-button' />
                </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    var id = '{$id}';
    var window_id   = '{$window_id}';
    new EasyEdits('/edits/workflow/actor','actor_'+id,{ '&id&': id, '&window_id&': window_id });
    $('#config-actor-form [name=workflow_id]').val(Paradigm.actions.get.mongoWorkflowId());
    Form.intercept($('#config-actor-form').get(),'{$id}','/workflow/elements/listener',window_id)
</script>