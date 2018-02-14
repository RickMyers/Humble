{assign var=id value=$element->getId()}
{assign var=data value=$element->load()}
{assign var=windowId value=$helper->getWindowId()}
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
</style>
<table style="width: 100%; height: 100%">
    {if ($data)}
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">{$data.type}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">{$data.shape}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$data.id}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{if (isset($data['namespace']))}{$data['namespace']}{else}N/A{/if}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">{if (isset($data['method']))}{$data['method']}{else}N/A{/if}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">{if (isset($data['component']))}{$data['component']}{else}N/A{/if}</div></td>
    </tr>
    {/if}
    <tr>
        <td colspan="3" valign="middle" align="center">
            <form name="terminus-form" id="config-terminus-form-{$id}">
                <input type="hidden" name="id" id="element-id-{$id}" value="{$id}" />
                <input type="hidden" name="windowId" id="window-id-{$id}" value="{$windowId}" />
                <table>
                    <tr>
                        <td>
                            <div style='margin-left: auto; margin-right: auto; width: 660px; padding: 10px; background-color: #F0f0f0; color: black'>
                            <form nohref>
                                <div style='width: 600px; padding: 10px 0px; font-family: sans-serif; font-size: 1.3em; font-weight: bold'>
                                    Inbound Webservice: <i style='font-family: monospace'>/wapi/{$data['uri']}</i>
                                </div>
                                <div>
                                    <fieldset style="padding: 10px"><legend>Webservice Status</legend>
                                    <input type="checkbox" name="enabled" id="webservice-enabled-{$windowId}" {if ($webservice->getActive()=="Y")}checked{/if} value="Y" />  - When this box is checked, the webservice is available
                                </div>
                            <br />
                            {assign var=h1 value='Variable Name'}
                            {assign var=h2 value='Source'}
                            {assign var=h3 value='Format'}
                            Expects These Parameters:
                            <ul style='font-family: monospace;'>
                                <li style='white-space: pre; list-style: none; background-color: #d0d0d0; color: black; font-weight: bold'>{$h1|str_pad:20}{$h2|str_pad:12}{$h3|str_pad:14}</li>
                            {foreach from=$helper->JSON($data['parameters']) item=parameter}
                                <li style='white-space: pre'>{$parameter.name|str_pad:20}{$parameter.source|str_pad:12}{$parameter.format|str_pad:14}</li>
                            {/foreach}
                            </ul><br />
                            And Uses This Security Protocol:<br /><br />
                            {if ($data['security-scheme']=='standard')}
                                <fieldset style='width: 600px; margin-left: auto; margin-right: auto; padding: 10px 20px'><legend>Standard</legend>
                                    The <i>Standard</i> scheme works by passing the User Id and Password with every request to the webservice URI.<br /><br />
                                    <table>
                                        <tr>
                                            <td style='background-color: #d0d0d0; color: black; padding: 2px; font-weight: bold;'>User Id:</td>
                                            <td style='font-family: monospace; padding-left: 30px'>{$data['standard-userid']}</td>
                                        </tr>
                                        <tr>
                                            <td style='background-color: #d0d0d0; color: black; padding: 2px; font-weight: bold;'>Password:</td>
                                            <td style='font-family: monospace; padding-left: 30px'>{$data['standard-password']}</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            {elseif ($data['security-scheme']=='token')}
                                <fieldset style='width: 600px; margin-left: auto; margin-right: auto; padding: 10px 20px'><legend>Enigma</legend>
                                    The <i>Token</i> scheme works in the simplest way.  You must pass this token variable with the token value to gain trigger this workflow<br /><br />
                                    <table>
                                        <tr>
                                            <td style='background-color: #d0d0d0; color: black; padding: 2px; font-weight: bold;'>Token Variable:</td>
                                            <td style='font-family: monospace; padding-left: 30px'>{$data['token-variable']}</td>
                                        </tr>
                                        <tr>
                                            <td style='background-color: #d0d0d0; color: black; padding: 2px; font-weight: bold;'>Token Value:</td>
                                            <td style='font-family: monospace; padding-left: 30px'>{$data['token-value']}</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            {elseif ($data['security-scheme']=='enigma')}
                                <fieldset style='width: 600px; margin-left: auto; margin-right: auto; padding: 10px 20px'><legend>Enigma</legend>
                                    The <i>Enigma</i> scheme works by requesting a secure token by passing the User Id and Password to the creation URI below.
                                    When the number of seconds passes the token will expire, and you will need to request a new token.  To get a new token, pass the
                                    old token to the renewal URI below.  This way, a secure token is only valid for a short period of time.<br /><br />
                                    <table>
                                        <tr>
                                            <td style='background-color: #d0d0d0; color: black; padding: 2px; font-weight: bold;'>User Id:</td>
                                            <td style='font-family: monospace; padding-left: 30px'>{$data['enigma-userid']}</td>
                                        </tr>
                                        <tr>
                                            <td style='background-color: #d0d0d0; color: black; padding: 2px; font-weight: bold;'>Password:</td>
                                            <td style='font-family: monospace; padding-left: 30px'>{$data['enigma-password']}</td>
                                        </tr>
                                        <tr>
                                            <td style='background-color: #d0d0d0; color: black; padding: 2px; font-weight: bold;'>Creation URI:</td>
                                            <td style='font-family: monospace; padding-left: 30px'>/wapi/{$data['enigma-create']}</td>
                                        </tr>
                                        <tr>
                                            <td style='background-color: #d0d0d0; color: black; padding: 2px; font-weight: bold;'>Renewal URI:</td>
                                            <td style='font-family: monospace; padding-left: 30px'>/wapi/{$data['enigma-renew']}</td>
                                        </tr>
                                        <tr>
                                            <td style='background-color: #d0d0d0; color: black; padding: 2px; font-weight: bold;'>Token Expires:</td>
                                            <td style='font-family: monospace; padding-left: 30px'>{$data['enigma-expire']} seconds</td>
                                        </tr>

                                    </table>
                                </fieldset>


                                 <br />


                            {else}
                                I'm not sure what I got here: {$data['security-scheme']}<br /><br />
                            {/if}
                            </form>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    $('#webservice-enabled-{$windowId}').on("click",function () {
        var active = this.checked ? 'Y' : 'N';
        (new EasyAjax('/paradigm/webservice/activate')).add('id','{$webservice->getId()}').add('active',(this.checked ? 'Y' : 'N')).callback(function (response) {
            console.log(response);
        }).post();
    });
</script>
