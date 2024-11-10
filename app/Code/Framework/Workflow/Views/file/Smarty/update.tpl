{assign var=window_id value=$file->getWindowId()}
<table style="width: 100%; height: 100%">
    <tr>
        <td valign="middle" align="center">
            <h3 style="font-family: sans-serif; color: navy">Updated...</h3>
        </td>
    </tr>
</table>
<script type="text/javascript">
    var tt = function () {
        if (Desktop.window.list['{$window_id}']) {
            Desktop.window.list['{$window_id}']._close();
        }
    }
    window.setTimeout(tt,3000)
</script>
