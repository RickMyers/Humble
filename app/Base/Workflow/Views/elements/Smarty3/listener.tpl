<table style="width: 100%; height: 100%">
    <tr>
        <td align="center">
            <h2 style="color: navy">Event Listener Established</h2>
        </td>
</table>
<script type="text/javascript">
    window.setTimeout(function () {
        var w = Desktop.window.list['{$manager->getWindowId()}'];
        if (w) {
            w._close();
        }
    },2200);
</script>