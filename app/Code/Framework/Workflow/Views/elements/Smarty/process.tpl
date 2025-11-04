<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px; padding-bottom: 10px
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
                Initial element configuration.  To begin configuring this process element, please choose the appropriate
                container object below, and then choose what action you'd like this process to perform.  Afterwards a detailed
                configuration panel will appear if applicable
            </div>
            <div id="process-nav-{$id}" style='margin-left: auto; margin-right: auto; width: 545px'>
            </div>
            <div id="process-internal-tab-{$id}" style='margin-left: auto; margin-right: auto; width: 545px'>
                <form name='process-form' id='humble-paradigm-config-process-internal-form-{$manager->getId()}' onsubmit='return false'>
                    <input type="hidden" name="id" id="element-id-{$manager->getId()}" value="{$manager->getId()}" />
                    <input type="hidden" name="process" value="Y" />   
                    <img src='/images/paradigm/clipart/process.png' style='float: right' />
                    <div>
                        <div>
                            <select name='namespace' id='humble-paradigm-config-process-internal-namespace-{$manager->getId()}'>
                                <option value=''>Please choose from this list</option>
                                {foreach from=$modules->fetch() item="module"}
                                    <option value='{$module.namespace}'>{$module.namespace|ucfirst}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class='form-field-description'>Available Object Collections</div>
                        <div>
                        <select name='component' id='humble-paradigm-config-process-internal-component-{$manager->getId()}'>
                            <option value=''>Please choose from this list</option>
                        </select>
                        </div>
                        <div class='form-field-description'>Available Process Objects</div>
                        <div style='white-space: nowrap; position: relative'>
                            <select name='method' id='humble-paradigm-config-process-internal-method-{$manager->getId()}'>
                                <option value=''>Please choose from this list</option>
                            </select><input type="text" placeholder='Select from below or create a new method' name="method" id="humble-paradigm-config-process-internal-method-{$manager->getId()}_combo" value="" />
                                  <img id='view_code-{$manager->getId()}' src='/images/workflow/view_code.png' title='View Code' style='height: 22px; position: relative; top:6px; margin-right: 4px; cursor: pointer; visibility: hidden' />
                        </div>
                        <div class='form-field-description'>Available Process Methods</div>
                    </div>
                    <input type='button' name='save' id='humble-paradigm-config-process-internal-save-{$manager->getId()}' style='display: inline-block' />
                    <div style='display: inline-block; width: 450px; min-height: 20px; border: 1px solid #aaf; padding: 5px; background-color: #F0F0D0; border-radius: 5px ' id='config-component-comment-{$manager->getId()}'></div>
                    <script type='text/javascript'>
                        var ee = new EasyEdits(null,'process_internal-{$manager->getId()}');
                        ee.fetch('/edits/workflow/internalprocess');
                        ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
                        Form.intercept($('#humble-paradigm-config-process-internal-form-{$manager->getId()}').get(),'{$manager->getId()}',false,'{$manager->getWindowId()}');
                        $('#view_code-{$manager->getId()}').on('click',(evt)=>{
                            var win = Desktop.semaphore.checkout(true);
                            win = Desktop.semaphore.checkout(true);  //BECAUSE REASONS!!!!
                            (new EasyAjax('/workflow/elements/explore')).add('window_id',win.id).packageForm('humble-paradigm-config-process-internal-form-{$manager->getId()}').then((response) => {
                                win._title('Model Explore')._scroll(false)._open(response);
                            }).post();
                        });
                    </script>                
                </form>
            </div>
            <div id="process-external-tab-{$id}" style='margin-left: auto; margin-right: auto; width: 545px'>
                <div style='margin-left: auto; margin-right: auto; width: 545px'>
                    <img src='/images/paradigm/clipart/process.png' style='float: right' />
                    
                    <form name='process-form' id='humble-paradigm-config-process-external-form-{$id}' onsubmit='return false'>
                    <input type="hidden" name="window_id" id="window-id-external-{$manager->getId()}" value="{$manager->getWindowId()}" />
                    <input type="hidden" name="id" id="humble-paradigm-config-process-external-id-{$id}" value="{$manager->getId()}" />                      
                    <input type="hidden" name="process" value="Y" />   
                    <select name='namespace' id='humble-paradigm-config-process-external-namespace-{$id}'>
                        <option value=''>Please choose from this list</option>
                        {foreach from=$externals item="dirs"}
                            <option value='{$dirs.namespace}'>{$dirs.namespace}</option>
                        {/foreach}
                    </select>
                    <div class='form-field-description'>Available External Sources</div>
                    <br />
                    <select name='component' id='humble-paradigm-config-process-external-component-{$id}'>
                        <option value=''>Please choose from this list</option>
                    </select>
                    <div class='form-field-description'>Available External Process Objects</div><br />
                    <select name='method' id='humble-paradigm-config-process-external-method-{$id}'>
                        <option value=''>Please choose from this list</option>
                    </select>
                    <div class='form-field-description'>Available External Process Methods</div>
                    <br />
                    <div style='float: right; display: none; width: 470px; border: 1px solid #aaf; padding: 5px 10px; background-color: #F0F0D0; border-radius: 10px ' id='config-component-comment-{$manager->getId()}'></div>
                    <input type='button' name='decision-form-save' id='humble-paradigm-config-process-external-save-{$id}' />
                    </form>
                    <script>
                        var ee = new EasyEdits(null,'process_external-{$manager->getId()}');
                        ee.fetch('/edits/workflow/externalprocess');
                        ee.process(ee.getJSON().replace(/&id&/g,'{$manager->getId()}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
                        Form.intercept($('#humble-paradigm-config-process-external-form-{$id}').get(),'{$manager->getId()}',false,'{$manager->getWindowId()}');                          
                    </script>
                </div>                
            </div>
            </form>
        </td>
    </tr>
</table>
<script type='text/javascript'>
    (() => {
        let tabs = new EasyTab('process-nav-{$id}',120);
        tabs.add('Internal',null,'process-internal-tab-{$id}');
        tabs.add('External',null,'process-external-tab-{$id}');
        tabs.tabClick(0);
    })();
</script>