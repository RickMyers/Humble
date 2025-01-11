{assign var="workflow" value=$generator->generate()}
<div id="generate-workflow-tabs">

</div>
<div id="generator-tabs-area" style="position: relative; ">
    <div id="generated-workflow-tab" style="position: relative">
        <textarea name="generated-workflow-text" spell='false' id="generated-workflow-text" style="font-family: monospace;width: 100%; height: 100%">{$workflow}</textarea>
    </div>
    <div id="generated-image-tab" style="position: relative">
        <img id="generated-workflow-image" src="{$generator->getImage()}" />
    </div>
    <div id="generated-json-tab" style="position: relative">
        <textarea name="generator-json-text" spell='false' id="generator-json-text" style="font-family: monospace; width: 100%; height: 100%">
            {$generator->getJson()}
        </textarea>
    </div>

</div>
<div id="generator-controls-area">
    <div style="clear: both"></div>
</div>
<script type="text/javascript">
    var gtb = new EasyTab('generate-workflow-tabs');
    gtb.add('Workflow',null,'generated-workflow-tab');
    gtb.add('Image',null,'generated-image-tab');
    gtb.add('JSON',null,'generated-json-tab');
    gtb.tabClick(0);
    var win = Desktop.window.list['{$generator->getWindowId()}'];

    win.resize(() => {
            Paradigm.actions.set.mongoWorkflowId('{$generator->_workflowId()}');
            $('#generator-tabs-area').height($(win.content).height() - $('#generate-workflow-tabs').height() -15);
            var h = $('#generator-tabs-area').height();
            var w = $('#generator-tabs-area').width() - 15;
            $('#generator-json-text').height(h);
            $('#generator-json-text').width(w);
            $('#generated-workflow-text').height(h);
            $('#generated-workflow-text').width(w);
            $('#generated-workflow-image').height(h);
    });
    win._resize();
</script>