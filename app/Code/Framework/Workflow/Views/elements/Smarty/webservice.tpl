{assign var=data      value=$element->load()}
{assign var=id        value=$manager->getId()}
{assign var=window_id value=$manager->getWindowId()}
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
            <form name='webservice-trigger-form' id='webservice-trigger-form' onsubmit='return false'>
            <input type="hidden" name="window_id"   value="{$window_id}" />
            <input type="hidden" name="id"          value="{$id}" />
            <input type="hidden" name="workflow_id" value="" />
            <input type='hidden' name='parameters'  value='' />
            <input type='hidden' name='namespace'   value='' />
            <input type='hidden' name='component'   value='Integration' />
            <input type='hidden' name='method'      value='IEFBR14' />
            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                Initial Component Configuration
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                Initial element configuration.  To begin configuring this webservice element, please set the URI that will trigger
                the workflow, and then choose how you'd like to manage the security settings
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px'>
                <img src='/images/paradigm/clipart/webservice2.png' style='float: right; height: 100px;' />
                /esb/<input type='text' value="{if (isset($data.uri))}{$data.uri}{/if}" placeholder='your/URI/here' class='security-input-text' style='width: 265px' name='uri' />
                <div class='form-field-description'>Webservice URI</div><br />
                <table cellspacing='1' cellpadding='2'>
                    <tr>
                        <th style='text-align: left'>Name</th>
                        <th style='text-align: left'>Source</th>
                        <th style='text-align: left' colspan='2'>Type</th>
                    </tr>
                    <tr>
                        <td>
                            <input class='parameter-input-text' type='text' name='parameter' />
                        <td>
                            <select class='parameter-input-select' name='source'>
                                <option value='POST'>POST</option>
                                <option value='GET'>GET</option>
                                <option value='PUT'>PUT</option>
                                <option value='DELETE'>DELETE</option>
                                <option value='FILE'>FILE</option>
                                <option value='REQUEST'>REQUEST</option>
                            </select>
                        </td>
                        <td>
                            <select class='parameter-input-select' name='format'>
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
                        <td colspan='4' id='humble-parameters-display'>

                        </td>
                </table>
                <div class='form-field-description'>Inbound Parameters</div><br /><br />
                <div>
                    <fieldset style="padding: 10px"><legend>Webservice Status</legend>
                    <input type="checkbox" name="enabled" {if ($webservice->getActive()=="Y")}checked{/if} value="Y" />  - When this box is checked, the webservice is available
                    </fieldset>
                </div><br /><br />
                <div id='humble-paradigm-config-webservice-security-nav'></div>
                <div id='humble-paradigm-config-webservice-security-none' style='display: none; padding: 30px'>
                    <table cellspacing='1'>
                        <tr>
                            <td colspan='2'>
                                <input type='radio' {if (isset($data['security-scheme']) && ($data['security-scheme']=='none'))}checked="checked"{/if} name='security-scheme' value='none' /> None (Passthru)<br /><br />
                            </td>
                        </tr>
                    </table>
                </div>
                <div id='humble-paradigm-config-webservice-security-session' style='display: none; padding: 30px'>
                    <table cellspacing='1'>
                        <tr>
                            <td colspan='2'>
                                <input type='radio' {if (isset($data['security-scheme']) && ($data['security-scheme']=='session'))}checked="checked"{/if} name='security-scheme' value='session' /> Session ID (sessionId)<br /><br />
                                You must have authenticated previously and you are passing the Session ID in the variable "sessionId"
                            </td>
                        </tr>
                    </table>
                </div>
                <div id='humble-paradigm-config-webservice-security-standard' style='display: none; padding: 30px'>
                    <table cellspacing='1'>
                        <tr>
                            <td colspan='2'>
                                <input {if (isset($data['security-scheme']) && ($data['security-scheme']=='standard'))}checked="checked"{/if} type='radio' name='security-scheme' value='standard' /><br /><br />
                            </td>
                        </tr>

                        <tr>
                            <td>User Id: </td><td><input type='text' value="{if (isset($data['standard-userid']))}{$data['standard-userid']}{/if}" class='security-input-text' name='standard-userid' />
                        </tr>
                        <tr>
                            <td>Password: </td><td><input type='text' value="{if (isset($data['standard-password']))}{$data['standard-password']}{/if}" class='security-input-text' name='standard-password' />
                        </tr>
                    </table>
                </div>
                <div id='humble-paradigm-config-webservice-security-bearer-token' style='display: none; padding: 30px'>
                    <table cellspacing='1'>
                        <tr>
                            <td colspan='2'>
                                <input type='radio' {if (isset($data['security-scheme']) && ($data['security-scheme']=='bearer'))}checked="checked"{/if} name='security-scheme' value='bearer'  /><br /><br />
                            </td>
                        </tr>
                    </table>
                </div>                        
                <div id='humble-paradigm-config-webservice-security-token' style='display: none; padding: 30px'>
                    <table cellspacing='1'>
                        <tr>
                            <td colspan='2'>
                                <input type='radio' {if (isset($data['security-scheme']) && ($data['security-scheme']=='api'))}checked="checked"{/if} name='security-scheme' value='api' /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>API Variable: </td><td><input type='text' class='security-input-text' name='token-variable' />
                        </tr>
                        <tr>
                            <td>API Token: </td><td><input type='text' class='security-input-text' name='token-value' />
                        </tr>
                    </table>
                </div>
                <div id='humble-paradigm-config-webservice-security-whitelist-tab' style='display: none; padding: 30px'>
                    <input type="checkbox" name="use-whitelist" id="humble-paradigm-config-webservice-security-use-whitelist" /> Whitelist?<br /><br />
                    <textarea name="whitelist" rows="5" cols="55" style="font-family: monospace; font-size: .9em"></textarea>
                </div>
                <div class='form-field-description'>Security Scheme</div><br />
                <input type='submit' value=' Save ' style='background-color: #115883; color: white; border: 1px solid silver; padding: 2px 5px; border-radius: 2px' name='webservice-save' />
                <hr />
                &copy; Humble Project, 2014-present, all rights reserved
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    Form.intercept($('#webservice-trigger-form').get(),'{$id}','/workflow/webservice/save','{$window_id}');
    var tabs = new EasyTab('humble-paradigm-config-webservice-security-nav');
    tabs.add('None', null,'humble-paradigm-config-webservice-security-none');
    tabs.add('Session', null,'humble-paradigm-config-webservice-security-session');
    tabs.add('Standard', null,'humble-paradigm-config-webservice-security-standard');
    tabs.add('API Token',null,'humble-paradigm-config-webservice-security-token');
    tabs.add('Bearer Token',null,'humble-paradigm-config-webservice-security-bearer-token');
    tabs.add('Whitelist',null,'humble-paradigm-config-webservice-security-whitelist-tab');
    tabs.tabClick(0);
    $('#webservice-trigger-form [name=workflow_id]').val(Paradigm.actions.get.mongoWorkflowId());
    $('#webservice-trigger-form [name=namespace]').val(Paradigm.actions.get.namespace());

    var WebserviceParameter = (function ($) {
        var parameters = '{$element->getParameters()}';
        if (parameters) {
            parameters = JSON.parse(parameters);
        } else {
            parameters = [];
        }
        $('#webservice-parameters').val(JSON.stringify(parameters))
        var display = $E('humble-parameters-display');
        return {
            add: function () {
                parameters[parameters.length] = {
                    "name":   $('#webservice-trigger-form [name=parameter]').val(),
                    "source": $('#webservice-trigger-form [name=source]').val(),
                    "format": $('#webservice-trigger-form [name=format]').val()
                }
                $('#webservice-trigger-form [name=parameter]').val('');
                $('#webservice-trigger-form [name=parameters]').val(JSON.stringify(parameters));
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
                $('#webservice-parameters').val(JSON.stringify(parameters));
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