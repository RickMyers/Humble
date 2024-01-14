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
                <form name="designer-create-form" id="designer-create-form" onsubmit="return false;">
                    <fieldset style="padding: 0px 20px"><legend>Instructions</legend>
                        Please enter the required fields below and either identify a URL to pull the form from or upload an image from your computer to serve as the form<br /><br />
                    <input type="text" name="name" id="form_name" /><br />
                    Form Name<br /><br />
                    <textarea type="text" name="description" id="form_description"  ></textarea><br />
                    Description<br /><br />
                    <input type="text" name="url" id="form_url" /><br />
                    Form Image URL<br /><br />
                    <b>OR</b><br /><br />
                    <input type="file" name="image" id="form_image" />
                    Form Upload<br /><br />
                    <input type="button" value=" Create Form " id="form_create_button" name="form_create_button" />
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
    new EasyEdits('/edits/paradigm/create','designer-create-form');
</script>
