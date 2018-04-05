<script type="text/javascript">
    var winId = '{$manager->getWindowId()}';
    Desktop.window.list[winId]._content("<table style='width: 100%; height: 100%'><tr><td align='center' valign='middle'><img src='/images/paradigm/clipart/loading_indicator.gif' /></td></tr></table>");
    (new EasyAjax('/paradigm/element/configure')).add('namespace','{$manager->getNamespace()}').add('window_id',winId).add('id','{$manager->getId()}').add('type','{$manager->getType()}').thenfunction (response) {
        Desktop.window.list[winId]._content(response);
    }).post();
</script>
