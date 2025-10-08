
<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px
    }
</style>
{if (isset($method))}
    
    
    
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
</style>
<table style="width: 100%; height: 100%; border-spacing: 1px;">
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Type</div><div class="paradigm-config-field">&nbsp;</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Shape</div><div class="paradigm-config-field">&nbsp;</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Mongo ID</div><div class="paradigm-config-field">{$manager->getId()}</div></td>
    </tr>
    <tr style="height: 30px">
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Namespace</div><div class="paradigm-config-field">{$manager->getNamespace()}</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">Event</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">{$method}</div></td>

    </tr>
    <tr>
        <td colspan="3" align="center" valign="middle">
            <form id='trigger-form-{$manager->getId()}' name="trigger-form" onsubmit='return false'>
                <input type="hidden" name="window_id"   value="{$manager->getWindowId()}" />
                <input type="hidden" name="id"          value="{$manager->getId()}" />
                <input type="hidden" name="component"   value="Event" />      
                <input type="hidden" name="namespace"   value="{$manager->getNamespace()}" />      
                <input type="hidden" name="event"       value="{$method}" />
                <input type="hidden" name="workflow_id"  id='workflow_id-{$manager->getId()}' value="" />
                <table>
                    <tr>
                        <td>
                            <fieldset style="width: 600px; padding: 10px"><legend>Instructions</legend>
                                <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                                    Initial Component Configuration
                                </div>
                                <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                                    Trigger details are below.  If the checkbox is marked, then this event is "live" and can
                                    be used to trigger one or more workflows
                                </div>
                                <div style='margin-left: auto; margin-right: auto; width: 545px'>
                                    <img src='/images/paradigm/clipart/trigger.png' style='float: right' />
                                    <table>
                                        <tr>
                                            <td style="text-align: right; padding-right: 10px"><b>Namespace</b>: </td>
                                            <td>{$manager->getNamespace()}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; padding-right: 10px"><b>Event</b>: </td>
                                            <td>{$method}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" >&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" ><input type="checkbox" name="active" value="Y" {if (isset($data.active)&& ($data.active=='Y'))}checked="checked" {/if} />
                                            When this checkbox is checked, this event is active and can be triggered</td>
                                        </tr>                    
                                    </table>
                                    <br /><br /><input type='submit' name='save' value=" Save " id='humble-paradigm-config-trigger-form-save' />
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    $('#workflow_id-{$manager->getId()}').val(Paradigm.actions.get.currentDiagramId());
   Form.intercept($('#trigger-form-{$manager->getId()}').get(),'{$manager->getId()}','/paradigm/trigger/save',false,false,false, (thing,event,data) => {
       
    Desktop.window.list['{$window_id}'].set('<table style="width: 100%; height: 100%"><tr><td align="center">Event Listener Saved</td></tr></table>');
    var f = function () {
        Desktop.window.list['{$window_id}']._close();
    }
    window.setTimeout(f,3000);
   });
</script>    
   
{else}
<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>
            <form name='process-form' id='humble-paradigm-config-trigger-form' onsubmit='return false'>
            <input type="hidden" name="window_id" id="window-id" value="{$manager->getWindowId()}" />
            <input type="hidden" name="id" id="element-id" value="{$manager->getId()}" />
            <input type="hidden" name="component" id="component-id" value="Event" />
            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                Initial Component Configuration
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                Initial element configuration.  To begin configuring this trigger element, please choose the appropriate
                container object below, and then choose what action you'd like this process to perform.  Afterwards a detailed
                configuration panel will appear if applicable
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px'>
                <img src='/images/paradigm/clipart/trigger.png' style='float: right' />
                <select name='namespace' id='humble-paradigm-config-trigger-form-namespace'>
                    <option value=''>Please choose from this list</option>
                    {foreach from=$events->uniqueNamespaces() item="event"}
                        {if (!$event.namespace)}
                            <option style="font-style: italic" value='-1'>(empty)</option>
                        {else}
                            <option value='{$event.namespace}'>{$event.namespace|ucfirst}</option>
                        {/if}
                    {/foreach}
                </select>
                <div class='form-field-description'>Available Object Collections</div>
                <br />
                <select name='method' id='humble-paradigm-config-trigger-form-events'>
                    <option value=''>Please choose from this list</option>
                </select>
                <div class='form-field-description'>Available Events</div><br />
                <br />
                <div style='float: right; display: none; width: 470px; border: 1px solid #aaf; padding: 5px 10px; background-color: #F0F0D0; border-radius: 10px ' id='config-component-comment'></div>
                <input type='button' name='save' id='humble-paradigm-config-trigger-form-save' />
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    var ee = new EasyEdits(null,'trigger_{$manager->getId()}');
       
    
    ee.fetch('/edits/workflow/trigger');
    ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
    Form.intercept($('#humble-paradigm-config-trigger-form').get(),'{$manager->getId()}',false,false,false,false,function (thing,event,data) {
        
       // $('#humble-paradigm-config-trigger-form').submit();
        console.log(data);
    });
</script>
{/if}