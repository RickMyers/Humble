<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px
    }
</style>
{assign var=id value=$manager->getId()}
<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>
            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                Initial Component Configuration
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                Initial element configuration.  To begin configuring this decision element, please choose the appropriate
                container object below, and then choose what action you'd like this decision to evaluate.  Afterwards a detailed
                configuration panel will appear if applicable
            </div>
            <div id="decision-nav-{$id}" style='margin-left: auto; margin-right: auto; width: 545px'>
            </div>
            <div id="decision-internal-{$id}">
                <div style='margin-left: auto; margin-right: auto; width: 545px'>
                    <form name='decision-form' id='humble-paradigm-config-internal-decision-form-{$id}' onsubmit='return false'>
                    <input type="hidden" name="window_id" id="window-id-internal-{$manager->getId()}" value="{$manager->getWindowId()}" />
                    <input type="hidden" name="id" id="humble-paradigm-config-internal-decision-form-id-{$id}" value="{$manager->getId()}" />      
                    <input type="hidden" name="decision" value="Y" />   
                    <img src='/images/paradigm/clipart/decision.png' style='float: right' />
                    <select name='namespace' id='humble-paradigm-config-internal-decision-form-namespace-{$id}'>
                        <option value=''>Please choose from this list</option>
                        {foreach from=$modules->fetch() item="module"}
                            <option value='{$module.namespace}'>{$module.namespace|ucfirst}</option>
                        {/foreach}
                    </select>
                    <div class='form-field-description'>Available Object Collections</div>
                    <br />
                    <select name='component' id='humble-paradigm-config-internal-decision-form-component-{$id}'>
                        <option value=''>Please choose from this list</option>
                    </select>
                    <div class='form-field-description'>Available Decision Objects</div><br />
                    <select name='method' id='humble-paradigm-config-internal-decision-form-method-{$id}'>
                        <option value=''>Please choose from this list</option>
                    </select>
                    <div class='form-field-description'>Available Decision Methods</div>
                    <br />
                    <div style='float: right; display: none; width: 470px; border: 1px solid #aaf; padding: 5px 10px; background-color: #F0F0D0; border-radius: 10px ' id='config-component-comment-{$manager->getId()}'></div>
                    <input type='button' name='decision-form-save' id='humble-paradigm-config-internal-decision-form-save-{$id}' />
                    </form>
                    <script>
                        var ee = new EasyEdits(null,'decision_internal_{$manager->getId()}');
                        ee.fetch('/edits/workflow/internaldecision');
                        ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
                        Form.intercept($('#humble-paradigm-config-internal-decision-form-{$manager->getId()}').get(),'{$manager->getId()}',false,'{$manager->getWindowId()}');                        
                    </script>
                </div>
                
            </div>
            <div id="decision-external-{$id}">
                <div style='margin-left: auto; margin-right: auto; width: 545px'>
                    <img src='/images/paradigm/clipart/decision.png' style='float: right' />
                    <form name='decision-form' id='humble-paradigm-config-external-decision-form-{$id}' onsubmit='return false'>
                    <input type="hidden" name="window_id" id="window-id-external-{$manager->getId()}" value="{$manager->getWindowId()}" />
                    <input type="hidden" name="id" id="humble-paradigm-config-external-decision-form-id-{$id}" value="{$manager->getId()}" />    
                    <input type="hidden" name="decision" value="Y" />   
                    <select name='namespace' id='humble-paradigm-config-external-decision-form-namespace-{$id}'>
                        <option value=''>Please choose from this list</option>
                        {foreach from=$externals item="dirs"}
                            <option value='{$dirs.namespace}'>{$dirs.namespace}</option>
                        {/foreach}
                    </select>
                    <div class='form-field-description'>Available External Sources</div>
                    <br />
                    <select name='component' id='humble-paradigm-config-external-decision-form-component-{$id}'>
                        <option value=''>Please choose from this list</option>
                    </select>
                    <div class='form-field-description'>Available External Decision Objects</div><br />
                    <select name='method' id='humble-paradigm-config-external-decision-form-method-{$id}'>
                        <option value=''>Please choose from this list</option>
                    </select>
                    <div class='form-field-description'>Available External Decision Methods</div>
                    <br />
                    <div style='float: right; display: none; width: 470px; border: 1px solid #aaf; padding: 5px 10px; background-color: #F0F0D0; border-radius: 10px ' id='config-component-comment-{$manager->getId()}'></div>
                    <input type='button' name='decision-form-save' id='humble-paradigm-config-external-decision-form-save-{$id}' />
                    </form>
                    <script>
                        var ee = new EasyEdits(null,'decision_external_{$manager->getId()}');
                        ee.fetch('/edits/workflow/externaldecision');
                        ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
                        Form.intercept($('#humble-paradigm-config-external-decision-form-{$manager->getId()}').get(),'{$manager->getId()}',false,'{$manager->getWindowId()}');                          
                    </script>
                </div>
                
            </div>
            
        </td>
    </tr>
</table>
<script type='text/javascript'>
    (() => {
        let tabs = new EasyTab('decision-nav-{$id}',120);
        tabs.add('Internal',null,'decision-internal-{$id}');
        tabs.add('External',null,'decision-external-{$id}');
        tabs.tabClick(0);
    })();
</script>
