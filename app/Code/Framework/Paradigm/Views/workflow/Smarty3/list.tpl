<style type='text/css'>
    .workflow-field {
        background-color: #dadada; margin: 1px
    }
    .workflow-field-description {
        font-family: serif; font-size: .8em; letter-spacing: 3px; padding-left: 3px
    }
    .workflow-field-value {
        font-family: sans-serif; font-size: 1em; text-align: right; padding-right: 3px; height: 18px
    }
</style>
<script type="text/javascript">
    Workflows.clear();
</script>
<table cellspacing='0' cellpadding='0' style='width: 100%; height: 100%;'>
    <tr>
        <td colspan="2" style="height: 70px; border-bottom: 1px dotted #777;">
            <div style='float: right'>
                <form name='workflow-namespace-list-form' id='workflow-namespace-list-form' onsubmit='return false'>
                    <br />
                    <select style='width: 220px; margin-right: 5px' name='workflow-namespace-list' id='workflow-namespace-list'>
                        <option value=''> </option>
                    </select><br />
                    <div style='font-size: .8em; letter-spacing: 3px'>Namespace</div>
                </form>
            </div>
            <img src="/images/paradigm/clipart/load.png" style='float: left; height: 70px; margin-right: 6px' />
            <div style='font-family: sans-serif; font-size: 3.5em'>Load Workflow</div>
            <div style='font-family: sans-serif; font-size: .9em; letter-spaceing: 3px'>Mouse over workflow to view a preview</div>
        </td>
    </tr>
    <tr>
        <td rowspan="2" style="min-width: 250px; width: 35%; position: relative;  padding-top: 5px" valign="top">
            <div id='available-workflows'></div>
            <div style='position: absolute; bottom: 10px; width: 100%'>
                <form nohref onsubmit='return false'>
                    <center>
                        <table>
                            <tr>
                                <td><input onclick="Workflows.previous()" type='button' class='settingsButton' style='font-size: .9em; font-family: sans-serif; padding: 1px 3px' value=' < ' /> </td>
                                <td><input onclick="Workflows.first()" type='button' class='settingsButton' style='font-size: .9em; font-family: sans-serif; padding: 1px 3px' value='  <<  ' /> </td>
                                <td><input type='text' style='text-align: center; width: 50px; border: 0px' id='workflow-open-page-number' name='workflow-open-page-number' /> </td>
                                <td><input onclick="Workflows.last()" type='button' class='settingsButton' style='font-size: .9em; font-family: sans-serif; padding: 1px 3px' value='  >>  ' /> </td>
                                <td><input onclick="Workflows.next()" type='button' class='settingsButton' style='font-size: .9em; font-family: sans-serif; padding: 1px 3px' value=' > ' /> </td>
                            </tr>
                        </table>
                    </center>
                </form>
            </div>
        </td>
        <td style="height: 80px; border-left: 1px dotted #777;">
            <table style="width: 100%; min-width: 350px; border-collapse: separate; border-spacing: 2px" >
                <tr>
                    <td class='workflow-field'>
                        <div class="workflow-field-description">Title</div>
                        <div class="workflow-field-value" id="workflow-title"></div>
                    </td>
                    <td colspan='' class='workflow-field'>
                        <div class="workflow-field-description">Creator</div>
                        <div class="workflow-field-value" id="workflow-creator"></div>
                    </td>
                    <td class='workflow-field'>
                        <div class="workflow-field-description">Version</div>
                        <div class="workflow-field-value" id="workflow-version"></div>
                    </td>
                </tr>
                <tr>
                    <td style='width: 33%' class='workflow-field'>
                        <div class="workflow-field-description">Saved</div>
                        <div class="workflow-field-value" id="workflow-saved"></div>
                    </td>
                    <td style='width: 33%' class='workflow-field'>
                        <div class="workflow-field-description">Modified</div>
                        <div class="workflow-field-value" id="workflow-modified"></div>
                    </td>
                    <td style='width: 33%' class='workflow-field'>
                        <div class="workflow-field-description">Generated</div>
                        <div class="workflow-field-value" id="workflow-generated"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan='3' class='workflow-field'>
                        <div class="workflow-field-description">Description</div>
                        <div class="workflow-field-value" id="workflow-description"></div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="border-left: 1px dotted #777; overflow: hidden; position: relative;" valign='middle' align='center'>
            <div style="width: 100%; height: 100%; overflow: hidden">
            <img id='workflow-preview' src='/images/paradigm/clipart/preview.png' onerror="this.src='/images/paradigm/clipart/not_available.jpg'" />
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="border-top: 1px dotted #777; height: 30px; font-family: sans-serif; padding-bottom: 5px; font-size: .8em">
            &copy; Humble Project 2014-present,
        </td>
    </tr>
</table>
<script type='text/javascript'>
    new EasyEdits('/edits/paradigm/namespaces','workflow-list');
</script>