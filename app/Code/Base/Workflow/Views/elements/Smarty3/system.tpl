<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .85em; letter-spacing: 2px
    }
</style>
{assign var=id value=$manager->getId()}
{assign var=window_id value=$manager->getWindowId()}
{assign var=data value=$element->load()}
<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>

            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                System Event Configuration.
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                A system event occurs at a particular time of day, and may be recurring based upon a predefined interval.
                There are two types of events, the single event and the recurring.  Choose the date to begin the event, and then the time.  If it is a recurring event,
                mark it as so, and then choose the interval, otherwise the workflow will only be triggered once for the time and date selected.<br /><br />
                <b>Please don't forget to hit &lt;<u>Save</u>&gt; to set the trigger date and time!
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px'>
                <form name='humble-paradigm-config-system-event-form' id='humble-paradigm-config-system-event-form-{$id}' onsubmit='return false'>
                <input type="hidden" id="humble-paradigm-config-system-event-form-id-{$id}" name="id" value="{$id}" />
                <input type="hidden" name="workflow_id" id="workflow_id-{$id}" value="" />
                <input type="hidden" name="window_id" id="window_id-{$id}" value="{$window_id}" />
                <input type="hidden" name="windowId" id="windowId-{$id}" value="{$window_id}" />
                <input type="hidden" id="event_date-{$id}" name="event_date" value="{if (isset($data.event_date))}{$data.event_date}{/if}" />
                <input type="hidden" id="event_time-{$id}" name="event_time" value="{if (isset($data.event_time))}{$data.event_time}{/if}" />
                <div class='form-field-description'>Date and Time to launch workflow</div><br />
                <div id="event-date-picker-{$id}" style=""></div>
                <div style="height: 290px; "></div>
                <div style="clear: both"></div>
                <input type="checkbox" value='Y' {if (isset($data.recurring_flag))} {if ($data.recurring_flag == 'Y')}checked{/if}{/if} name="recurring_flag" id="event_recurring_flag-{$id}" /> Repeatable Event
                <input style='margin-left: 40px' type="checkbox" value='Y' {if (isset($data.active_flag))} {if ($data.active_flag == 'Y')}checked{/if}{/if} name="active_flag" id="event_active_flag-{$id}" /> Activate <br /><br />                
                <select name="period" id="recurring_event_period-{$id}">
                    <option value=""></option>
                    <option value="900">Every 15 Minutes</option>
                    <option value="1800">Every 30 Minutes</option>
                    <option value="3600">Every Hour</option>
                    <option value="43200">Every 12 Hours</option>
                    <option value="86400">Every Day</option>
                    <option value="604800">Every Week</option>
                    <option value="1209600">Bi-Weekly</option>
                    <option value="monthly">Every Month</option>
                    <option value="yearly">Every Year</option>
                </select>
                <div class='form-field-description'>Period Of Occurrence</div>
                <br />
                <br />
                
                <input type="button" id="event_trigger-{$id}" name="event_trigger" value=" Set Event " />
                <br />
                </form>
            </div>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    {if (isset($data.interval))}
        $('#recurring_event_interval-{$id}').val('{$data.interval}');
    {/if}
    {if (isset($data.period))}
        $('#recurring_event_period-{$id}').val('{$data.period}');
    {/if}        
    $('#workflow_id-{$id}').val(Paradigm.actions.get.mongoWorkflowId());
    var id = '{$id}';
    var window_id   = '{$window_id}';
    Desktop.window.list[window_id]._scroll(true);
    $( "#event-date-picker-{$id}" ).filthypillow( {
            exitOnBackgroundClick: false,
            {if (isset($data.event_date))}
                initialDateTime:  function (m) {
                                    return moment("{$data.event_date} {$data.event_time} YYYY-MM-DD HH:mm"); 
                                  },
            {/if}            
            calendar: {
                isPinned: true
            },
            minDateTime: function( ) {
              return moment( ).subtract( "days", 365 );
            },
            maxDateTime: function( ) {
              return moment( ).add( "days", 365 );
            }
    });
    $( "#event-date-picker-{$id}" ).filthypillow( "show" );    
    
    $('#event-date-picker-{$id}').on("fp:save",function (e, stamp) {
        if (stamp) {
            $('#event_date-{$id}').val(stamp.format('YYYY-MM-DD'));
            $('#event_time-{$id}').val(stamp.format('HH:mm'));
        }
    });
    var ee = new EasyEdits(null,'system_'+id);
    ee.fetch('/edits/paradigm/system');
    ee.process(ee.getJSON().replace(/&id&/g,id).replace(/&window_id&/g,window_id));
    Form.intercept($('#humble-paradigm-config-system-event-form-{$id}').get(),'{$manager->getId()}','/paradigm/system/save',window_id);
</script>