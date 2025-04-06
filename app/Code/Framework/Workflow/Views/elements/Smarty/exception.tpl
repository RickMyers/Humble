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
    .field-desc {
        padding-bottom: 20px; font-family: monospace; letter-spacing: 2px
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
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Method</div><div class="paradigm-config-field">Workflow</div></td>
        <td class="paradigm-config-cell"><div class="paradigm-config-descriptor">Component</div><div class="paradigm-config-field">Exception</div></td>
    </tr>
    {/if}
    <tr>
        <td colspan="3" valign="middle" align="center">
            <form name="module-exception-form" id="module-exception-form-{$id}">
                <input type="hidden" name="id" id="element-id-{$id}" value="{$id}" />
                <input type="hidden" name="window_id" id="window-id-{$id}" value="{$window_id}" />
                <fieldset style="width: 50%"><legend>Exception Class</legend>
                    <table>
                        <tr>
                            <td>
                                <select name="module" id="module-{$id}">
                                    <option value=""> </option>
                                    {foreach from=$modules->fetch() item=module}
                                        <option value="{$module.namespace}"> {$module.module} [{$module.namespace}]</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="field-desc">Module/Namespace</td>
                        </tr>
                        <tr>
                            <td>
                                <select name="exception" id="exception-{$id}">
                                    <option value=""> </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="field-desc">Exception</td>
                        </tr>
                        <tr>
                            <td><input type="text" name="rc" id="exception_rc-{$id}" value="{if (isset($data.rc))}{$data.rc}{/if}" /></td>
                        </tr>
                        <tr>
                            <td class="field-desc">Return Code [RC]</td>
                        </tr>

                        <tr>
                            <td><input id="exception_form_save-{$id}" name="exception_form_save" type="submit" value=" Save "></td>
                        </tr>                    
                    </table>
                </fieldset>
            </form>
        </td>
    </tr>
    <tr>
        <td colspan="3" valign="middle" align="center">
            <form name="adhoc-exception-form" id="adhoc-exception-form-{$id}">
                <input type="hidden" name="id" id="adhoc-element-id-{$id}" value="{$id}" />
                <input type="hidden" name="window_id" id="adhoc-window-id-{$id}" value="{$window_id}" />
                <fieldset style="width: 50%"><legend>Adhoc Exception</legend>
                    <table>
                        <tr>
                            <td><input type="text" name="title" id="title-{$id}" value="{if (isset($data.title))}{$data.title}{/if}" /></td>
                        </tr>
                        <tr>
                            <td class="field-desc">Error Title</td>
                        </tr>
                        <tr>
                            <td><input type="text" name="message" id="message-{$id}" value="{if (isset($data.message))}{$data.message}{/if}" /></td>
                        </tr>
                        <tr>
                            <td class="field-desc">Error Title</td>
                        </tr>
                        <tr>
                            <td><input type="text" name="rc" id="adhoc_rc-{$id}" value="{if (isset($data.rc))}{$data.rc}{/if}" /></td>
                        </tr>
                        <tr>
                            <td class="field-desc">Return Code [RC]</td>
                        </tr>
                        <tr>
                            <td><input id="adhoc_form_save-{$id}" name="adhoc_form_save" type="submit" value=" Save "></td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Form.intercept($('#module-exception-form-{$id}').get(),'{$id}','/paradigm/element/update','{$window_id}');
    Form.intercept($('#adhoc-exception-form-{$id}').get(),'{$id}','/paradigm/element/update','{$window_id}');
    (()=>{
        let e1 = new EasyEdits(null,'exception-{$id}');
        e1.fetch('/edits/workflow/exception');
        e1.process(e1.getJSON().replace(/&id&/g,'{$id}').replace(/&window_id&/g,'{$manager->getWindowId()}'));
        let e2 = new EasyEdits(null,'adhoc_exception-{$id}');
        e2.fetch('/edits/workflow/adhocexception');
        e2.process(e2.getJSON().replace(/&id&/g,'{$id}').replace(/&window_id&/g,'{$manager->getWindowId()}'));        
        
    })();
</script>
