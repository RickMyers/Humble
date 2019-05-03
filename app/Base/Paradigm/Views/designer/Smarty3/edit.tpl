<style type="text/css">
    .layer-editor-desc {
    }
    .layer-editor-field {
    }
    .designer-layer-edit-field {
        border: 1px solid #aaf; background-color: lightcyan
    }
</style>
<table style="width: 100%; height: 100%">
    <tr>
        <td style="background-color: #333; height: 35px; color: ghostwhite; font-size: 1.2em">
            Layer Editor
        </td>
    </tr>
    <tr>
        <td style="position: relative">
            <div style="width: 700px; padding: 10px; margin-left: auto; margin-right: auto">
                <form name="paradigm-designer-layer-editor-{$window_id}" id="paradigm-designer-layer-editor-{$window_id}">
                    <div style="width: 49.5%; height: 200px; display: inline-block; background-color: rgba(202,202,202,.3)">
                        <div style="background-color: rgba(50,50,50,.5)">
                            Basic Field Data
                        </div>
                        <table style="width: 100%;">
                            <tr>
                                <td class="layer-editor-desc">Field Name:</td>
                                <td class="layer-editor-field"><input type="text" name="field-name" id="designer-field-name-{$window_id}" /></td>
                            </tr>
                            <tr>
                                <td class="layer-editor-desc">Field Type:</td>
                                <td class="layer-editor-field">
                                    <select name="field-type" id="designer-field-type-{$window_id}">
                                        <option value=""></option>
                                        <option value="text"> Text </option>
                                        <option value="textarea"> Textarea </option>
                                        <option value="select"> Drop Down Menu</option>
                                        <option value="checkbox"> Checkbox </option>
                                        <option value="radio"> Radio Buttons </option>
                                        <option value="file"> File Upload </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="layer-editor-desc">Default:</td>
                                <td class="layer-editor-field"><input type="text" name="field-default" id="designer-field-default-{$window_id}" value='' /></td>
                            </tr>
                            <tr>
                                <td class="layer-editor-desc">Required:</td>
                                <td class="layer-editor-field"><input type="checkbox" name="field-required" id="designer-field-required-{$window_id}" value='Y' /></td>
                            </tr>
                        </table>
                    </div>
                    <div style="width: 49.5%; height: 200px; display: inline-block; background-color: rgba(202,202,202,.3)">
                        <div style="background-color: rgba(50,50,50,.5)">
                            Positional Data
                        </div>
                       <table style="width: 100%">
                        <tr>
                            <td class="layer-editor-desc">X: </td>
                            <td class="layer-editor-field"><input type='text' name='designer-layer-X' id='designer-layer-X-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                        </tr>
                        <tr>
                            <td class="layer-editor-desc">Y:</td>
                            <td class="layer-editor-field"><input type='text' name='designer-layer-Y' id='designer-layer-Y-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                        </tr>
                        <tr>
                            <td class="layer-editor-desc">Z:</td>
                            <td class="layer-editor-field"><input type='text' name='designer-layer-Z' id='designer-layer-Z-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                        </tr>
                        <tr>
                            <td class="layer-editor-desc">Width:</td>
                            <td class="layer-editor-field"><input type='text' name='designer-layer-W' id='designer-layer-W-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                        </tr>
                        <tr>
                            <td class="layer-editor-desc">Height:</td>
                            <td class="layer-editor-field"><input type='text' name='designer-layer-H' id='designer-layer-H-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                        </tr>

                        </table>
                    </div>
                    <div style="clear: both"></div>
                    <div style="width: 100%; height: 200px; display: inline-block; background-color: rgba(202,202,202,.3)">
                        <div style="background-color: rgba(50,50,50,.5)">
                            Extended Field Data
                        </div>
                        <table style="width: 100%">
                        <tr>
                            <td class="layer-editor-desc">Max Length:</td>
                            <td class="layer-editor-field"><input type='text' name='' id='-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                            <td class="layer-editor-desc">Min-Length:</td>
                            <td class="layer-editor-field"><input type='text' name='' id='-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                        </tr>
                        <tr>
                            <td class="layer-editor-desc">Mask:</td>
                            <td class="layer-editor-field"><input type='text' name='' id='-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                            <td class="layer-editor-desc">Max Chars:</td>
                            <td class="layer-editor-field"><input type='text' name='' id='-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                        </tr>
                        <tr>
                            <td class="layer-editor-desc">Font:</td>
                            <td class="layer-editor-field"></td>
                            <td class="layer-editor-desc">Font-Size:</td>
                            <td class="layer-editor-field"><input type='text' name='' id='-{$window_id}' style='' class='designer-layer-edit-field' /></td>
                        </tr>
                        </table>
                    </div>
               </form>
            </div>
        </td>
    </tr>
    <tr>
        <td style="background-color: #333; height: 35px; color: ghostwhite; font-size: .9em">
            &copy; The Humble Project
        </td>
    </tr>
</table>
<script type="text/javascript">
    (function () {
        var form  = Designer.forms[{$form_id}];
        var layer = form.layers['id_{$layer_id}'];
        console.log(layer);
        //$('#raw-layer-json-{$window_id}').val(JSON.stringify(layer));
        $('#designer-layer-X-{$window_id}').val((layer.startX ? layer.startX:''));
        $('#designer-layer-Y-{$window_id}').val((layer.startY ? layer.startY:''));
        $('#designer-layer-Z-{$window_id}').val((layer.Z ? layer.Z:''));
        $('#designer-layer-W-{$window_id}').val((layer.width ? layer.width:''));
        $('#designer-layer-H-{$window_id}').val((layer.height ? layer.height:''));
        //$('#').val((Designer.form.current.layer.current ? Designer.form.current.layer.current):'');
        //$('#').val((Designer.form.current.layer.current ? Designer.form.current.layer.current):'');
        //$('#').val((Designer.form.current.layer.current ? Designer.form.current.layer.current):'');
        //$('#').val((Designer.form.current.layer.current ? Designer.form.current.layer.current):'');
        $('#designer-layer-X-{$window_id}').on('change',function (evt) {
            Designer.forms[{$form_id}].layers['id_{$layer_id}'].startX = evt.target.value;
            Designer.render();
        });
        $('#designer-layer-Y-{$window_id}').on('change',function (evt) {
            Designer.forms[{$form_id}].layers['id_{$layer_id}'].startY = evt.target.value;
            Designer.render();
        });
        $('#designer-layer-W-{$window_id}').on('change',function (evt) {
            Designer.forms[{$form_id}].layers['id_{$layer_id}'].width = evt.target.value;
            Designer.render();
        });
        $('#designer-layer-H-{$window_id}').on('change',function (evt) {
            Designer.forms[{$form_id}].layers['id_{$layer_id}'].height = evt.target.value;
            Designer.render();
        });
    })();
</script>