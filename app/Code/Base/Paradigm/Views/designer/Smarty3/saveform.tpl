
<style type="text/css">
</style>
<table style='width: 100%; height: 100%'>
    <tr>
        <td style='background-color: #333; height: 20px'>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td>
            <div style='width: 600px; margin-left: auto; margin-right: auto; padding: 10px'>
                <form name="designer-save-form" id="designer-save-form" onsubmit="return false;">
                    <fieldset style="padding: 0px 20px"><legend>Instructions</legend>
                        Review the data below and click "Save Form" to, well, save the form!<br /><br />
                    <input type="text" name="name" id="save_form_name" /><br />
                    Form Name<br /><br />
                    <textarea type="text" name="description" id="save_form_description"  ></textarea><br />
                    Description<br /><br />
                    Number of layers: <span id="save_form_number_of_layers">0</span><br /><br />
                    <input type="button" value=" Save Form " id="save_form_button" name="save_form_button" />
                    </fieldset>
                </form>
            </div>
        </td>
    </tr>
    <tr>
        <td style='background-color: #333; height: 20px'>

        </td>
    </tr>
</table>
<script type="text/javascript">
    new EasyEdits('/edits/paradigm/saveform','designer-save-form');
    $('#save_form_name').val(Designer.form.current.name);
    $('#save_form_description').val(Designer.form.current.description);
    var c = 0;
    for (var i in Designer.form.current.layers){
        c++;
    }
    $('#save_form_number_of_layers').html(c);
</script>
