{assign var=data value=$element->load()}
<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px
    }
    .parameter-input-text {
        background-color: lightcyan; width: 150px; border: 1px solid #aaf; padding: 2px; border-radius: 2px;
    }
    .parameter-input-text:focus {
        background-color: yellow;
    }
    .parameter-input-select {
        background-color: lightcyan; width: 100px; border: 1px solid #aaf; padding: 2px; border-radius: 2px;
    }
    .parameter-input-select:focus {
        background-color: yellow;
    }
    .security-input-text {
        background-color: lightcyan; width: 100px; border: 1px solid #aaf; padding: 2px; border-radius: 2px;
    }
    .security-input-text:focus {
        background-color: yellow;
    }
    .security-input-select {
        background-color: lightcyan; width: 100px; border: 1px solid #aaf; padding: 2px; border-radius: 2px;
    }
    .security-input-select:focus {
        background-color: yellow;
    }
    .webservice-parameter-header {
        float: left; width: 100px; margin-right: 1px; background-color: #d0d0d0; font-family: sans-serif; font-weight: bold; font-size: .9em; padding: 2px
    }
    .webservice-parameter-cell {
        float: left; width: 100px; margin-right: 1px; font-family: sans-serif;  font-size: .8em; padding: 2px
    }
    .webservice-parameter-remove {
        text-decoration: none; padding: 1px 3px; background-color: red; border: 1px solid silver; border-radius: 3px; color: silver; font-weight: bold; margin-right: 2px
    }
</style>
<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>
            <form name='webservice-form' id='humble-paradigm-config-webservice-form-{$manager->getId()}' onsubmit='return false'>
            <input type="hidden" name="window_id"        id="window-id-{$manager->getId()}" value="{$window_id}" />
            <input type="hidden" name="id"              id="element-id-{$manager->getId()}" value="{$manager->getId()}" />
            <input type="hidden" name="workflow_id"     id="workflow-id-{$manager->getId()}" value="" />
            <input type='hidden' name='parameters'      id='webservice-parameters-{$manager->getId()}' value='' />
            <input type='hidden' name='namespace'       id='webservice-namespace-{$manager->getId()}' value='' />
            <input type='hidden' name='component'       id='webservice-component-{$manager->getId()}' value='Integration' />
            <input type='hidden' name='method'          id='webservice-method-{$manager->getId()}' value='IEFBR14' />
            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                Initial Component Configuration
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                Initial element configuration.  To begin configuring this webservice element, please set the URI that will trigger
                the workflow, and then choose how you'd like to manage the security settings
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px'>
                <img src='/images/paradigm/clipart/webservice2.png' style='float: right; height: 100px;' />
                /esb/<input type='text' value="{if (isset($data.uri))}{$data.uri}{/if}" placeholder='your/URI/here' class='security-input-text' style='width: 265px' name='uri' id='humble-paradigm-config-webservice-uri-{$manager->getId()}' />
                <div class='form-field-description'>Webservice URI</div><br />
                <table cellspacing='1' cellpadding='2'>
                    <tr>
                        <th style='text-align: left'>Name</th>
                        <th style='text-align: left'>Source</th>
                        <th style='text-align: left' colspan='2'>Type</th>
                    </tr>
                    <tr>
                        <td>
                            <input class='parameter-input-text' type='text' name='parameter' id='humble-paradigm-config-webservice-parameter-name-{$manager->getId()}' />
                        <td>
                            <select class='parameter-input-select' name='source' id='humble-paradigm-config-webservice-parameter-source-{$manager->getId()}'>
                                <option value='POST'>POST</option>
                                <option value='GET'>GET</option>
                                <option value='PUT'>PUT</option>
                                <option value='DELETE'>DELETE</option>
                                <option value='FILE'>FILE</option>
                                <option value='REQUEST'>REQUEST</option>
                            </select>
                        </td>
                        <td>
                            <select class='parameter-input-select' name='format' id='humble-paradigm-config-webservice-parameter-format-{$manager->getId()}'>
                                <option value='string'>String</option>
                                <option value='int'>Integer</option>
                                <option value='float'>Float</option>
                                <option value='boolean'>Boolean</option>
                                <option value='password'>Password</option>
                                <option value='json'>JSON</option>
                                <option value='isodate'>ISO Date [yyyy-mm-dd]</option>
                                <option value='displaydate'>Display Date [mm/dd/yyyy]</option>
                                <option value='*'>Any (*)</option>
                            </select>
                        </td>
                        <td>
                            <input onclick='WebserviceParameter.add()' type='button' onclick='return false;' value='+' style='background-color: #115883; color: white; font-weight: bold; font-size: 1.3em; border: 1px solid silver; width: 25px; height: 24px'
                        </td>
                    </tr>
                    <tr>
                        <td colspan='4' id='humble-parameters-display-{$manager->getId()}'>

                        </td>
                </table>
                <div class='form-field-description'>Inbound Parameters</div><br /><br />
                <div>
                    <fieldset style="padding: 10px"><legend>Webservice Status</legend>
                    <input type="checkbox" name="enabled" id="webservice-enabled-{$window_id}" {if ($webservice->getActive()=="Y")}checked{/if} value="Y" />  - When this box is checked, the webservice is available
                    </fieldset>
                </div><br /><br />
                <div id='humble-paradigm-config-webservice-security-nav-{$manager->getId()}'></div>
                <div id='humble-paradigm-config-webservice-security-none-{$manager->getId()}' style='display: none; padding: 30px'>
                    <table cellspacing='1'>
                        <tr>
                            <td colspan='2'>
                                <input type='radio' {if (isset($data['security-scheme']) && ($data['security-scheme']=='none'))}checked="checked"{/if} name='security-scheme' value='none' id='humble-paradigm-config-webservice-security-none-scheme-{$manager->getId()}' /> None (Passthru)<br /><br />
                            </td>
                        </tr>
                    </table>
                </div>
                <div id='humble-paradigm-config-webservice-security-session-{$manager->getId()}' style='display: none; padding: 30px'>
                    <table cellspacing='1'>
                        <tr>
                            <td colspan='2'>
                                <input type='radio' {if (isset($data['security-scheme']) && ($data['security-scheme']=='session'))}checked="checked"{/if} name='security-scheme' value='session' id='humble-paradigm-config-webservice-security-none-scheme-{$manager->getId()}' /> Session ID (sessionId)<br /><br />
                                You must have authenticated previously and you are passing the Session ID in the variable "sessionId"
                            </td>
                        </tr>
                    </table>
                </div>
                <div id='humble-paradigm-config-webservice-security-standard-{$manager->getId()}' style='display: none; padding: 30px'>
                    <table cellspacing='1'>
                        <tr>
                            <td colspan='2'>
                                <input {if (isset($data['security-scheme']) && ($data['security-scheme']=='standard'))}checked="checked"{/if} type='radio' name='security-scheme' value='standard' id='humble-paradigm-config-webservice-security-standard-scheme-{$manager->getId()}' /><br /><br />
                            </td>
                        </tr>

                        <tr>
                            <td>User Id: </td><td><input type='text' value="{if (isset($data['standard-userid']))}{$data['standard-userid']}{/if}" class='security-input-text' name='standard-userid' id='humble-paradigm-config-webservice-security-standard-userid-{$manager->getId()}' />
                        </tr>
                        <tr>
                            <td>Password: </td><td><input type='text' value="{if (isset($data['standard-password']))}{$data['standard-password']}{/if}" class='security-input-text' name='standard-password' id='humble-paradigm-config-webservice-security-standard-password-{$manager->getId()}' />
                        </tr>
                    </table>
                </div>
                <div id='humble-paradigm-config-webservice-security-token-{$manager->getId()}' style='display: none; padding: 30px'>
                    <table cellspacing='1'>
                        <tr>
                            <td colspan='2'>
                                <input type='radio' {if (isset($data['security-scheme']) && ($data['security-scheme']=='api'))}checked="checked"{/if} name='security-scheme' value='api' id='humble-paradigm-config-webservice-security-token-scheme-{$manager->getId()}' /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>API Variable: </td><td><input type='text' class='security-input-text' name='token-variable' id='humble-paradigm-config-webservice-security-token-variable-{$manager->getId()}' />
                        </tr>
                        <tr>
                            <td>API Token: </td><td><input type='text' class='security-input-text' name='token-value' id='humble-paradigm-config-webservice-security-token-value-{$manager->getId()}' />
                        </tr>
                    </table>
                </div>
                <div id='humble-paradigm-config-webservice-security-whitelist-tab-{$manager->getId()}' style='display: none; padding: 30px'>
                    <input type="checkbox" name="use-whitelist" id="humble-paradigm-config-webservice-security-use-whitelist-{$manager->getId()}" /> Whitelist?<br /><br />
                    <textarea name="whitelist" id="humble-paradigm-config-webservice-security-whitelist-{$manager->getId()}" rows="5" cols="55" style="font-family: monospace; font-size: .9em"></textarea>
                </div>
                <div class='form-field-description'>Security Scheme</div><br />
                <input type='submit' value=' Save ' style='background-color: #115883; color: white; border: 1px solid silver; padding: 2px 5px; border-radius: 2px' name='webservice-save' id='humble-paradigm-config-webservice-save-{$manager->getId()}' />
                <hr />
                &copy; Humble Project, 2014-present, all rights reserved
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
   // var ee = new EasyEdits(null,'webservice_{$manager->getId()}');
   // ee.fetch('/edits/workflow/webservice');
   // ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
    Form.intercept($('#humble-paradigm-config-webservice-form-{$manager->getId()}').get(),'{$manager->getId()}','/workflow/webservice/save');
    var tabs = new EasyTab('humble-paradigm-config-webservice-security-nav-{$manager->getId()}');
    tabs.add('None', null,'humble-paradigm-config-webservice-security-none-{$manager->getId()}');
    tabs.add('Session', null,'humble-paradigm-config-webservice-security-session-{$manager->getId()}');
    tabs.add('Standard', null,'humble-paradigm-config-webservice-security-standard-{$manager->getId()}');
    tabs.add('API Token',null,'humble-paradigm-config-webservice-security-token-{$manager->getId()}');
    tabs.add('Whitelist',null,'humble-paradigm-config-webservice-security-whitelist-tab-{$manager->getId()}');
    tabs.tabClick(0);
    $('#workflow-id-{$manager->getId()}').val(Paradigm.actions.get.mongoWorkflowId());
    $('#webservice-namespace-{$manager->getId()}').val(Paradigm.actions.get.namespace());

    var WebserviceParameter = (function ($) {
        var parameters = '{$element->getParameters()}';
        if (parameters) {
            parameters = JSON.parse(parameters);
        } else {
            parameters = [];
        }
        $('#webservice-parameters-{$manager->getId()}').val(JSON.stringify(parameters))
        var display = $E('humble-parameters-display-{$manager->getId()}');
        return {
            add: function () {
                parameters[parameters.length] = {
                    "name":   $('#humble-paradigm-config-webservice-parameter-name-{$manager->getId()}').val(),
                    "source": $('#humble-paradigm-config-webservice-parameter-source-{$manager->getId()}').val(),
                    "format": $('#humble-paradigm-config-webservice-parameter-format-{$manager->getId()}').val()
                }
                $E('humble-paradigm-config-webservice-parameter-name-{$manager->getId()}').value='';
                $('#webservice-parameters-{$manager->getId()}').val(JSON.stringify(parameters));
                WebserviceParameter.render();
            },
            remove: function (idx) {
                var parms = [];
                for (var i=0; i<parameters.length; i++) {
                    if (i !== idx) {
                        parms[parms.length] = parameters[i];
                    }
                }
                parameters = parms;
                $('#webservice-parameters-{$manager->getId()}').val(JSON.stringify(parameters));
                WebserviceParameter.render();
            },
            render: function () {
                var html = '<ul>';
                html += '<div><div class="webservice-parameter-header">Variable</div><div class="webservice-parameter-header">Source</div><div class="webservice-parameter-header">Format</div></div><div style="clear: both"></div>';
                for (var i=0; i<parameters.length; i++) {
                    html += '<div class="webservice-parameter-cell"><a class="webservice-parameter-remove" href="#" onclick="WebserviceParameter.remove('+i+'); return false">X</a>'+parameters[i].name+'</div>'+
                            '<div class="webservice-parameter-cell">'+parameters[i].source+'</div>'+
                            '<div class="webservice-parameter-cell">'+parameters[i].format+"</div>";
                    html += '<div style="clear: both"></div>';
                }
                html += '</ul>';
                display.innerHTML = html;
            }
        }
    })($);
    WebserviceParameter.render();
</script>