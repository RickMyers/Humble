<div id='code_explorer-{$window_id}' widget="codeBox" widgetScroll="100%" lang="php" style='white-space: nowrap' lexicon="/pages/js/ColorizerLanguages.json" class="humble-code font-family: monospace; overflow: hidden; width: 100%; height: 100%">{$util->fetchWorkflowElementCode()}</div>
<script>
    (()=>{
        Colorizer.scan($E('code_explorer-{$window_id}'));
        var ctr = 0;
        var win = Desktop.window.list['{$window_id}'];
        win.resize(() => {
            var cb = $E('code_explorer-{$window_id}');        
            return function () {
                console.log(++ctr);
                cb.style.width = win.content.offsetWidth+"px";
                cb.style.height = win.content.offsetHeight+"px";
            }
        });
        win.resize();
    })();
    
    
</script>
