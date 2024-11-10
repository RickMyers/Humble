{assign var=data value=$element->load()}
<style type="text/css">
    .paradigm-config-descriptor {
        font-size: .8em; font-family: serif; letter-spacing: 2px;
    }
    .paradigm-config-field {
        font-size: 1em; font-family: sans-serif; text-align: right; padding-right: 4px;
    }
    .paradigm-config-input {
        padding: 2px; border-radius: 2px; background-color: lightcyan; color: #333; width: 225px; border: 1px solid silver
    }
    .paradigm-config-cell {
        width: 33%; margin: 1px; background-color: #e8e8e8;  border: 1px solid #d0d0d0; padding-left: 2px
    }
    .text-config-cell {
         margin: 1px;   padding-left: 2px; padding-bottom: 10px
    }
</style>
<table style="width: 100%; height: 100%; border-spacing: 1px;">
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">{$data.type}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">{$data.shape}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$data.id}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{$data.namespace}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">{$data.method}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">{$data.component}</div></td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form name="config-text-message-form" id="config-text-message-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="window_id" id="window_id_{$data.id}" value="{$helper->getWindowId()}" />
                <fieldset style='padding: 10px; margin-left: auto; margin-right: auto; width: 550px'><legend style='font-weight: bolder'>Instructions</legend>
                    <p style='text-align: left'>
                        Please specify the phone number to text below.  At present, only one (1) number is allowed.  If you know the phone carrier, please specify it below or if you do not know the carrier,
                        then enter the number and click the magnifying glass icon and the carrier will attempt to be determined through a phone number directory lookup.
                    </p>
                    <br /><br />
                    <table>
                        <tr>
                            <td class="text-config-cell">Number: </td><td class="text-config-cell"><input class="paradigm-config-input" type="text" name="number" id="config-text-number-{$data.id}" value="{if (isset($data.number))}{$data.number}{/if}" /></td>
                        </tr>
                        <tr>
                            <td class="text-config-cell">Carrier: </td>
                            <td class="text-config-cell">
                                <select class="paradigm-config-input"  name="carrier" id="sms-carrier-{$data.id}">
                                    <option value=""> </option>
                                    {foreach from=$carriers->fetch() item=carrier}
                                        <option {if (isset($data.carrier) && $data.carrier && (($data.carrier == $carrier.sms_domain) || ($data.carrier == $carrier.mms_domain)))}selected="true"{/if} value="{if ($carrier.sms_domain)}{$carrier.sms_domain}{else}{$carrier.mms_domain}{/if}">{$carrier.carrier}</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-config-cell">Message: </td><td class="text-config-cell"><input class="paradigm-config-input" type="text" name="message" id="config-text-message-{$data.id}" value="{if (isset($data.message))}{$data.message}{/if}" /></td>
                        </tr>
                        <tr>
                            <td class="text-config-cell" style='padding-top: 10px' colspan="2"><input type="submit" value=" Save " /></td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </td>
    </tr>
    <tr>
        <td colspan="3" height="20" valign="top" style="font-family: sans-serif; font-size: .8em">&copy; 2014-Present, Humble Project, all rights reserved</td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#config-text-message-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$helper->getWindowId()}");
</script>