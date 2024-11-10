<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px
    }
</style>
{assign var=actor_id value=$manager->getId()}
{assign var=window_id value=$manager->getWindowId()}
<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>
            <form name='id' id='humble-paradigm-config-actor-form-{$actor_id}' onsubmit='return false'>
            <input type="hidden" name="window_id" id="window-id-{$manager->getId()}" value="{$manager->getWindowId()}" />
            <input type="hidden" id="humble-paradigm-config-actor-form-id-{$actor_id}" name="id" value="{$actor_id}" />
            <input type="hidden" name="workflow_id" id="workflow_id_{$manager->getId()}" value="" />
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
                <select name='namespace' id='humble-paradigm-config-actor-form-namespace-{$actor_id}'>
                    <option value=''>Choose...</option>
                    <option value='{$client->getNamespace()}'>{$client->getNamespace()} - {$client->getDescription()}</option>
                </select>
                <div class='form-field-description'>Workflow Owner</div>
                <br />
                <select name='component' id='humble-paradigm-config-actor-form-component-{$actor_id}'>
                    <option value=''>Please choose from this list</option>
                </select>
                <div class='form-field-description'>Available Object Collections</div>
                <br />

                <select name='method' id='humble-paradigm-config-actor-form-method-{$actor_id}'>
                    <option value=''>Please choose from this list</option>
                </select>
                <div class='form-field-description'>Available Process Methods</div>
                <br />
                <div style='float: right; display: none; width: 470px; border: 1px solid #aaf; padding: 5px 10px; background-color: #F0F0D0; border-radius: 10px ' id='config-component-comment-{$manager->getId()}'></div>
                <input type='button' name='save' id='humble-paradigm-config-actor-form-save-{$actor_id}' />
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    var actor_id = '{$actor_id}';
    var window_id   = '{$window_id}';
    var ee = new EasyEdits(null,'actor_'+actor_id);
    $('#workflow_id_{$manager->getId()}').val(Paradigm.actions.get.mongoWorkflowId());
    ee.fetch('/edits/workflow/actor');
    ee.process(ee.getJSON().replace(/&id&/g,actor_id).replace(/&window_id&/g,window_id));
    Form.intercept($('#humble-paradigm-config-actor-form-{$actor_id}').get(),'{$actor_id}','/workflow/elements/listener',window_id)
</script>