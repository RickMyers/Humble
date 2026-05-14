{assign var=id value=$manager->getId()}
{assign var=window_id value=$manager->getWindowId()}
{assign var=data value=$component->load()}
{assign var=returns value=$component->getReturns()}
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
            <div style="margin-left: auto; margin-right: auto; width: 550px; padding: 20px">
                <form name="terminus-form" id="config-terminus-form-{$id}">
                    <fieldset><legend>Instructions</legend>
                        <div style="text-align: justify; margin-top: 20px; margin-bottom: 20px">
                        Below specify whether this part of the workflow returns "True", for a good outcome, or "False" for a negative outcome.
                        If the negative outcome is bad enough, select whether to cancel bubbling (stop further workflow processing)
                        </div>
                        
                        <input type="hidden" name="id" id="element-id-{$id}" value="{$id}" />
                        <input type="hidden" name="window_id" id="window-id-{$id}" value="{$window_id}" />
                        <table style="cellpadding: 5px; cellspacing: 5px">
                            <tr>
                                <td style="text-align: right; padding-right: 10px">Return Value: </td><td><input type="radio" name="returns" {if (isset($data['returns']))}{if ($data['returns']=='1')} checked="checked"{/if}{/if} id="terminus-value-true-{$id}-true" value='1' /> True &nbsp;&nbsp; 
                                    <input type="radio" name="returns" id="terminus-value-false-{$id}-false" value="0" {if (isset($data['returns']))}{if ($data['returns']=='0')} checked="checked"{/if} {/if}/> False
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding-right: 10px">Cancel Bubble: </td>
                                <td><input type="checkbox" value="Y" name="cancel" id="terminus-cancel-bubble-{$id}-cancel" {if (isset($data['cancel']))}{if ($data['cancel']=='Y')} checked="checked"{/if}{/if} /></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 25px"><input type="submit" value=" Save "></td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
            </div>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#config-terminus-form-{$id}').get(),'{$id}','/paradigm/element/update','{$window_id}');
</script>