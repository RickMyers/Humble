{assign var=data value=$element->load()}
<style type="text/css">
    .paradigm-alert-descriptor {
        font-size: .8em; font-family: serif; letter-spacing: 2px;
    }
    .paradigm-alert-field {
        font-size: 1em; font-family: sans-serif; text-align: right; padding-right: 4px;
    }
    .paradigm-alert-cell {
        width: 33%; margin: 1px; background-color: #e8e8e8;  border: 1px solid #d0d0d0; padding-left: 2px
    }
</style>
<table style="width: 100%; height: 100%; border-spacing: 1px;">
    <tr style="height: 30px">
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Type</div><div class="paradigm-alert-field">{$data.type}</div></td>
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Shape</div><div class="paradigm-alert-field">{$data.shape}</div></td>
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Mongo ID</div><div class="paradigm-alert-field">{$data.id}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Namespace</div><div class="paradigm-alert-field">{$data.namespace}</div></td>
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Method</div><div class="paradigm-alert-field">{$data.method}</div></td>
        <td class="paradigm-alert-cell"><div class="paradigm-alert-descriptor">Component</div><div class="paradigm-alert-field">{$data.component}</div></td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form name="config-export-form" id="config-export-form-{$data.id}" onsubmit="return false">
                <input type="hidden" name="id" id="id_{$data.id}" value="{$data.id}" />
                <input type="hidden" name="windowId" id="windowId_{$data.id}" value="{$windowId}" />
                <fieldset style="width: 500px; padding: 20px"><legend>Date Range</legend>
                    <table>
                        <tr>
                            <td align="right">
                                Start Date:
                            </td>
                            <td>
                                <input style="background-color: lightcyan; padding: 2px; border: 1px solid #aaf; border-radius: 2px; width: 200px"
                                       type="text" name="start_date" id="config-startdate-{$data.id}" value="{if (isset($data.start_date))}{$data.start_date}{/if}" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">End Date:</td>
                            <td>
                                <input style="background-color: lightcyan; padding: 2px; border: 1px solid #aaf; border-radius: 2px; width: 200px"
                                       type="text" name="end_date" id="config-enddate-{$data.id}" value="{if (isset($data.end_date))}{$data.end_date}{/if}" />
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset style="width: 500px; padding: 20px"><legend>Export for a specific day</legend>
                    <table>
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td align="right">
                                Date:
                            </td>
                            <td>
                                <input style="background-color: lightcyan; padding: 2px; border: 1px solid #aaf; border-radius: 2px; width: 200px"
                                       type="text" name="date" id="config-date-{$data.id}" value="{if (isset($data.date))}{$data.date}{/if}" />
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset style="width: 500px; padding: 20px"><legend>Run Daily</legend>
                    <table>
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td align="right">
                                Daily:
                            </td>
                            <td>
                                <input type="checkbox" name="daily" id="config-daily-{$data.id}"  value="Y" {if ((isset($data.daily))&&($data.daily == 'Y'))}checked{/if} />
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <table>
                    <tr><td colspan="2">
                        <input type="submit" value=" Save " />
                        </td></tr>
                </table>

            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#config-export-form-{$data.id}').get(),'{$data.id}','/paradigm/element/update',"{$windowId}");
    $('#config-startdate-{$data.id}').datepicker();
    $('#config-enddate-{$data.id}').datepicker();
    $('#config-enddate-{$data.id}').datepicker();
</script>