<table style='width: 100%; height: 100%'>
    <tr>
        <td style='background-color: #aaf; height: 20px; border-bottom: 1px solid #999'>
            <b>Save Workflow Diagram</b>
        </td>
    </tr>
    <tr>
        <td style='vertical-align: middle'>

            <div style='width: 400px;  margin-left: auto; margin-right: auto'>
                <div>
                    <img src='/images/paradigm/icons/soft1.png' style='float: left' /><div style='font-size: 3em; padding-top: 10px'>Save Workflow</div>
                </div>
                <div style='clear: both'></div>
                <br />
                <form name='workflowSaveForm' id='workflowSaveForm' onsubmit='return false'>
                <input type='text' name='shortdesc' id='shortdesc' value="{$workflow->getName()}"/><br />
                <label for="shortdesc" style="font-size: .8em">Name of workflow</label><br />
                <br />
                <textarea name="description" id="description">{$workflow->getDescription()}</textarea><br />
                <label for="shortdesc" style="font-size: .8em">Description</label><br />
                <br />
                <input type="button" name="workflowSaveButton" id="workflowSaveButton" />
                </form>
            </div>
        </td>
    </tr>
    <tr>
        <td style='font-family: sans-serif;border-top: 1px solid #999; background-color: #aaf;  font-size: 1em; height: 20px; padding-bottom: 12px'>
            <div>&copy; 2014 Humble Project, all rights reserved</div>
        </td>
    </tr>
</table>

<script type='text/javascript'>
    new EasyEdits('/edits/paradigm/save','saveWorkflow');
</script>