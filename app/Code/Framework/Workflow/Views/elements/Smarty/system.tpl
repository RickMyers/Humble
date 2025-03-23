<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .85em; letter-spacing: 2px
    }
    .event-scheduler {
        font-family: sans-serif; text-align: center; font-size: 1.8em; background-color: ghostwhite; color: #333; border: 1px solid #333; font-weight: normal
    }
    .event-scheduler-monthname {
        font-family: monospace; font-size: 1.9em; font-weight: bolder;
    }
    .event-scheduler-weekday {
        background-color: #f0f0f0; border-radius: 4px;  padding: 8px 16px; cursor: pointer
    }
    .event-scheduler-weekend {
        background-color: #dfdfdf; border-radius: 4px;  padding: 8px 24px; cursor: pointer
    }
    .event-scheduler-daynames {
        font-family: monospace; font-weight: bolder; font-size: 1.9em
    }
    .event-scheduler-arrows {
        height: 28px; cursor: pointer; margin-top: 2px
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
                <input type="hidden" id="event_date-{$id}" name="event_date" value="{if (isset($data.event_date))}{$data.event_date}{/if}" />
                <input type="hidden" id="event_time-{$id}" name="event_time" value="{if (isset($data.event_time))}{$data.event_time}{/if}" />
                <div class='form-field-description'>Date and Time to launch workflow</div><br />
                <div id="event-date-picker-{$id}" style="float: left"></div>
                <select name="event_time_picker" id="event_time_picker-{$id}" style="margin-left: 4px; padding: 4px; font-size: 1.4em">
                </select>
                <div style="clear: both; margin-top: 10px;"><br /></div>
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
    var id = '{$id}';
    var window_id   = '{$window_id}';    
    var ee = new EasyEdits(null,'system_'+id);
    ee.fetch('/edits/paradigm/system');
    ee.process(ee.getJSON().replace(/&id&/g,id).replace(/&window_id&/g,window_id));
    Form.intercept($('#humble-paradigm-config-system-event-form-{$id}').get(),'{$manager->getId()}','/paradigm/system/save',window_id);
    ((window_id)=> {
        {if (isset($data.interval))}
            $('#recurring_event_interval-{$id}').val('{$data.interval}');
        {/if}
        {if (isset($data.period))}
            $('#recurring_event_period-{$id}').val('{$data.period}');
        {/if}
        $('#workflow_id-{$id}').val(Paradigm.actions.get.mongoWorkflowId());
        Desktop.window.list[window_id]._scroll(true);
        var now = new Date();
        var y = new EasyCalendar('event-date-picker-{$id}');
        y.setWeekday('event-scheduler event-scheduler-weekday').setWeekend('event-scheduler event-scheduler-weekend').setDayNames('event-scheduler event-scheduler-daynames').setMonthName('event-scheduler event-scheduler-monthname');
        y.setArrows('/images/paradigm/previous.png','/images/paradigm/next.png','event-scheduler-arrows');
        y.build();
        var timePicker = $E('event_time_picker-{$id}');
        //var hours = [8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7];
        var ints = ['00','15','30','45'];
        for (var i=0; i<24; i++) {
            for (j in ints) {
                var opt = document.createElement('option');
                opt.value = ((i<10) ? '0'+i : i)+':'+ints[j];
                opt.innerHTML = ((i<10) ? '0'+i : i)+':'+ints[j];
                if ((i==8) && (ints[j]=='00')) {
                    opt.selected = true;
                }
                timePicker.appendChild(opt);                
            }
        }
        let g = (evt) => {
            $('#event_time-{$id}').val($(evt.target).val());
        }
        $(timePicker).on('change',g);
        y.set(now.getMonth(),now.getFullYear());
        var f = function (mm,dd,yyyy) {
            mm++;
            this.clear();
            $('#event_date-{$id}').val(yyyy+'-'+mm+'-'+dd);
            $('#'+this.xref['d_'+yyyy+mm+dd]).css('background-color','red');
        }
        y.setDayHandler(f);
        y.onMonthChange = (calendar) => {
            if ($('#event_date-{$id}').val()) {
                var dates = $('#event_date-{$id}').val().split('-');
                if ((dates[1]-1 == calendar.thisMonth) && (dates[0]== calendar.thisYear)) {
                    $('#'+calendar.xref['d_'+dates[0]+(dates[1])+dates[2]]).css('background-color','red');
                }
            }
        };
         
    })(window_id);
</script>