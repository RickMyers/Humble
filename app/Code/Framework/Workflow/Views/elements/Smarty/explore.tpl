<div id='paradigm_ace_editor_{$window_id}' class='w-full border-2 border-black'>{$util->fetchWorkflowElementCode()}</div>
<script>
    (() => {
        var win = Desktop.window.list['{$window_id}'];
        console.log('{$window_id}');
        $('#paradigm_ace_editor_{$window_id}').height(win.content.height() - 4);
        ace_editors['{$window_id}'] = ace.edit("paradigm_ace_editor_{$window_id}");
        ace_editors['{$window_id}'].setTheme("ace/theme/monokai");
        ace_editors['{$window_id}'].session.setMode("ace/mode/php");   
        ace_editors['{$window_id}'].fileSource = '/var/www/html/app/{$util->getSourceFile()}';
        ace_editors['{$window_id}'].resize(true);
        ace_editors['{$window_id}'].scrollToLine('{$util->getMethodLineNumber()}',true,true,function () {})
        ace_editors['{$window_id}'].gotoLine('{$util->getMethodLineNumber()}',10,true);
        win.resize(() => {
            $('#paradigm_ace_editor_{$window_id}').height(win.content.height() - 4);
        }).resize();
       
    })();
</script>

