<style type='text/css'>
    .form-field-description {
        font-family: arial; font-size: .7em; letter-spacing: 2px; padding-bottom: 10px
    }
</style>
{assign var=id value=$manager->getId()}
{assign var=window_id value=$manager->getWindowId()}
<table style='width: 100%; height: 100%;'>
    <tr>
        <td valign='middle'>
            <div style='margin-left: auto; margin-right: auto; width: 545px; font-size: 2em; font-family: sans-serif; color: #333; border-bottom: 1px solid #777; margin-bottom: 6px'>
                Initial Component Configuration
            </div>
            <div style='margin-left: auto; margin-right: auto; width: 545px; margin-bottom: 25px'>
                Initial element configuration.  To begin configuring this generator element, please choose the appropriate
                container object below, and then choose what action you'd like this generator to perform.  For more information
                on Generators, see <a href="https://humbleprogramming.com/pages/Generators.htmls" target="_BLANK" style="color: blue">https://humbleprogramming.com/pages/Generators.htmls</a>.
            </div>
            <div id="generator-nav-{$id}" style='margin-left: auto; margin-right: auto; width: 545px'>
            </div>
            <div id="generator-internal-tab-{$id}" style='margin-left: auto; margin-right: auto; width: 545px'>
                <form name='generator-form' id='generator-form-{$id}' onsubmit='return false'>
                    <input type="hidden" name="id"        value="{$id}" />
                    <input type="hidden" name="window_id" value="{$window_id}" />   
                    <input type="hidden" name="generator" value="Y" />   
                    <img src='/images/paradigm/clipart/generator.png' style='float: right' />
                    <div>
                        <div>
                            <select name='namespace' style="padding: 5px">
                                <option value=''>Please choose from this list</option>
                                {foreach from=$modules->fetch() item="module"}
                                    <option value='{$module.namespace}'>{$module.namespace|ucfirst}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class='form-field-description'>Available Object Collections</div>
                        <div>
                        <select name='component' style="padding: 5px">
                            <option value=''>Please choose from this list</option>
                        </select>
                        </div>
                        <div class='form-field-description'>Available Generator Objects</div>
                        <div style='white-space: nowrap; position: relative'>
                            <select name='method' style="padding: 5px">
                                <option value=''>Please choose from this list</option>
                            </select>
                                  <img id='view_code-{$id}' src='/images/workflow/view_code.png' title='View Code' style='height: 22px; position: relative; top:6px; margin-right: 4px; cursor: pointer; visibility: hidden' />
                        </div>
                        <div class='form-field-description'>Available Process Methods</div>
                    </div>
                    <input type='button' name='save-button' style='display: inline-block' />
                    <div style='margin-top: 25px; width: 450px; height: 60px; overflow: auto; border: 1px solid #aaf; padding: 5px; background-color: #F0F0D0; border-radius: 5px ' id='config-component-comment-{$id}'></div>
                    <script type='text/javascript'>
                        new EasyEdits('/edits/workflow/generator','generator-{$id}',{ '&id&': '{$id}', '&window_id&': '{$window_id}' });
                        Form.intercept($('#generator-form-{$id}').get(),'{$id}',false,'{$window_id}');
                        $('#view_code-{$id}').on('click',(evt)=>{
                            var win = Desktop.semaphore.checkout(true);
                            win = Desktop.semaphore.checkout(true);  //BECAUSE REASONS!!!!
                            (new EasyAjax('/workflow/elements/explore')).add('window_id',win.id).packageForm('internal-generator-form-{$id}').then((response) => {
                                win._title('Model Explore')._scroll(false)._open(response);
                            }).post();
                        });
                    </script>                
                </form>
            </div>
        </td>
    </tr>
</table>
