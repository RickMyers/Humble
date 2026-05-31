{assign var=data value=$element->load()}
<style type="text/css">
    .paradigm-config-descriptor {
        font-size: .8em; font-family: serif; letter-spacing: 2px;
    }
    .paradigm-config-field {
        font-size: 1em; font-family: sans-serif; text-align: right; padding-right: 4px;
    }
    .paradigm-config-cell {
        width: 33%; margin: 1px; background-color: #e8e8e8;  border: 1px solid #d0d0d0; padding-left: 2px
    }
    .paradigm-config-form-field {
        padding: 2px; background-color: lightcyan; color: #333; border: 1px solid #aaf
    }
    .rules-header {
        display: inline-block; width: 30%; box-sizing: border-box; background-color: #d0d0d0; font-family: sans-serif; font-weight: bold; font-size: .9em; padding: 2px
    }
    .rules-cell {
        display: inline-block; margin:0px; box-sizing: border-box; width: 30%; font-family: sans-serif;  font-size: .8em; padding: 2px
    }
    .rules-remove {
        text-decoration: none; padding: 1px 3px; background-color: red; border: 1px solid silver; border-radius: 3px; color: silver; font-weight: bold; margin-right: 2px    
</style>
<table style="width: 100%; height: 100%; border-spacing: 1px;">
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">{$data.type}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">{$data.shape}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$data.id}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{if (isset($data.namespace))}{$data.namespace}{else}N/A{/if}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">Workflow</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">Rule</div></td>

    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form name="config-operation-form" id="form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$window_id}" />
                <input type="hidden" name="namespace" id="namespace_{$data.id}}" value="paradigm" />
                <input type='hidden' name='rules'     id='rules-{$manager->getId()}' value='' />
                <input type='hidden' name='component' id='rules-component-{$manager->getId()}' value='Workflow' />
                <input type='hidden' name='method'    id='rules-method-{$manager->getId()}' value='Rule' />                
                <div id="rules-display-{$manager->getId()}"> 
                </div>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    //Form.intercept(Form Reference,MongoDB ID,optional URL or just FALSE,Dynamic WindowID to Close After Saving);
    Form.intercept($('#form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$window_id}");
    var Rules = (($) => {
        var rules       = '{$element->getRules()}';
        var initialized = false;
        if (rules) {
            rules       = JSON.parse(rules);
        } else {
            rules       = [];
        }
        $('#rules-{$manager->getId()}').val(JSON.stringify(rules))
        var display = $E('rules-display-{$manager->getId()}');
        return {
            init: () => {
                initialized = true;
                console.log('Initing');
                new EasyEdits('/edits/paradigm/newrule','newrule-{$window_id}',{ '&&FORM&&': "form-{$data.id}" });
             
            },
            add: () => {
                rules[rules.length] = {
                    "name":   $('#rules-name-{$manager->getId()}').val(),
                    "source": $('#rules-source-{$manager->getId()}').val(),
                    "format": $('#rules-format-{$manager->getId()}').val()
                }
                $E('rules-name-{$manager->getId()}').value='';
                $('#rules-{$manager->getId()}').val(JSON.stringify(parameters));
                this.render();
            },
            remove: function (idx) {
                var parms = [];
                for (var i=0; i<rules.length; i++) {
                    if (i !== idx) {
                        parms[parms.length] = rules[i];
                    }
                }
                rules = parms;
                $('#rules-{$manager->getId()}').val(JSON.stringify(rules));
                this.render();
            },
            render: function () {
                var html = `
                <div style="margin-right: auto; margin-left: auto; width: 500px">
                    <div  class="rules-header">Field Name</div>
                    <div class="rules-header">Comparison</div>
                    <div class="rules-header">Value</div>
                    <div style="clear: both"></div>
                    <div  class="rules-cell"><input type="text" name="field_name" value="" /></div>
                    <div class="rules-cell"><select name="field_comparison">  
                                                <option value="Contains"> Contains </option>
                                                <option value="Does Not Contain"> Does Not Contain </option>
                                                <option value="Equals"> Equals ( == )</option> 
                                                <option value="Not Equal To"> Not Equal To ( !== )</option>
                                                <option value="Greater Than"> Greater Than ( > ) </option> 
                                                <option value="Less Than"> Less Than ( < ) </option>
                                                <option value="Greater Than Or Equal To"> Greater Than Or Equal To ( >= ) </option>
                                                <option value="Less Than Or Equal To"> Less Than Or Equal To ( <= ) </option>
                                            </select>
                    </div>
                    <div class="rules-cell"><input type="text" name="field_value" value="" /></div>
                </div>
                <div style="clear: both"></div>`;
                for (var i=0; i<rules.length; i++) {
                    html += '<div class="rules-cell"><a class="rules-remove" href="#" onclick="Rules.remove('+i+'); return false">X</a>'+rules[i].name+'</div>'+
                            '<div class="rules-cell">'+rules[i].source+'</div>'+
                            '<div class="rules-cell">'+rules[i].format+"</div>";
                    html += '<div style="clear: both"></div>';
                }
                display.innerHTML = html;
                if (!initialized) {
                    this.init();
                }
            }
        }
    })($);
    Rules.render();    
</script>