<img src='/images/paradigm/clipart/process.gif' style='position: absolute; top: 0px; left: 0px; opacity: .4' />
<table style='width: 100%; height: 100%; position: relative; z-index: 2'>
    <tr>
        <td align='center' valign='middle'>
            <div style='width: 600px; margin-left: auto; margin-right: auto; text-align: left; padding: 10px 20px 5px 20px; font-size: 2.2em; font-family: sans-serif;background-color: #4F8DC0; color: white;'>
                Create a new Workflow Diagram
            </div>
            <div style='width: 600px; margin-left: auto; margin-right: auto; background-color: #4F8DC0; color: white; padding: 0px 20px 20px 20px; text-align: left'>
                <form name='paradigm-new-diagram-form' id='paradigm-new-diagram-form' onsubmit='return false'>
                    <select name='paradigm-new-diagram-client' id='paradigm-new-diagram-client'>
                        <option value='' style='font-style: italic'>Choose...</option>
                        {foreach from=$clients->fetch() item=client}
                            <option value='{$client.namespace}'>{$client.namespace} - {$client.description}</option>
                        {/foreach}
                    </select><br />
                    Client<br /><br />
                <input type='text' name='paradigm-new-diagram-title' id='paradigm-new-diagram-title' /><br />
                Title<br /><br />
                <textarea name='paradigm-new-diagram-description' id='paradigm-new-diagram-description'></textarea><br />
                Description<br /><br />
                <label title="A partial workflow is one that is meant to be called from another workflow using an off-page connector"><input type="checkbox" name="paradigm-partial-diargram" id="paradigm-partial-diagram" value='Y' /> This will be a partial workflow</label><br /><br />
                <input type='button' id='paradigm-new-diagram-save-button' name='paradigm-new-diagram-save-button' />
                </form>
            </div>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    new EasyEdits('/edits/paradigm/new','newDiagramForm');
</script>