{assign var=ok value=false}
{foreach from=$unit_tests->packageOrder() item=package}
    <div style='background-color: rgba(52,52,102,.7); color: white; padding: 5px 2px'>
        {$package|ucfirst}
    </div>
    <div style='white-space: nowrap; overflow: hidden; margin-bottom: 1px'>
        <div style='background-color: rgba(202,202,202,.2); padding: 5px 2px; display: inline-block; min-width: 150px; width: 15%; margin-right: 1px; font-weight: bold'>
            Unit Test Case
        </div>
        <div style='background-color: rgba(202,202,202,.2); padding: 5px 2px; display: inline-block; min-width: 300px; width: 50%; margin-right: 1px; font-weight: bold'>
            Description
        </div>
        <div style='background-color: rgba(202,202,202,.2); padding: 5px 2px; display: inline-block; min-width: 130px; width: 10%; margin-right: 1px; font-weight: bold'>
            Belongs to
        </div>
        <div style='background-color: rgba(202,202,202,.2); padding: 5px 2px; display: inline-block; min-width: 100px; width: 15%; margin-right: 1px; font-weight: bold'>
            Coverage
        </div>
        <div style='background-color: rgba(202,202,202,.2); padding: 5px 2px; display: inline-block; min-width: 100px; width: 10%; margin-right: 1px; text-align: center; font-weight: bold'>
            Results
        </div>
    </div>

    {foreach from=$unit_tests->fetchTests($package) item=unit_test}
        {assign var=test value=$unit_test->attributes()}
        <div style='white-space: nowrap; overflow: hidden; margin-bottom: 1px'>
            <div style='background-color: rgba(202,202,202,.2); padding: 5px 6px; display: inline-block; min-width: 150px; width: 15%; margin-right: 2px'>
                {$test->class}
            </div>
            <div style='background-color: rgba(202,202,202,.2); padding: 5px 6px; display: inline-block; min-width: 300px; width: 50%; margin-right: 2px'>
                {$test->description}
            </div>
            <div style='background-color: rgba(202,202,202,.2); padding: 5px 6px; display: inline-block; min-width: 130px; width: 10%; margin-right: 2px'>
                {$test->namespace}
            </div>
            <div style='background-color: rgba(202,202,202,.2); padding: 5px 6px; display: inline-block; min-width: 100px; width: 15%; margin-right: 2px'>
                &nbsp;
            </div>
            <div style='background-color: rgba(202,202,202,.2); padding: 5px 2px; display: inline-block; min-width: 100px; width: 10%; margin-right: 2px; text-align: center'>
                <div style='background-color: transparent; width: 6px; height: 8px; border: 1px solid #333; display: inline-block; margin-right: 3px; margin-top: 1px'>

                </div>
                <div style='background-color: transparent; width: 6px; height: 8px; border: 1px solid #333; display: inline-block; margin-right: 3px; margin-top: 1px'>

                </div>
                <div style='background-color: transparent; width: 6px; height: 8px; border: 1px solid #333; display: inline-block; margin-right: 3px; margin-top: 1px'>

                </div>
            </div>
        </div>
        {assign var=ok value=true}
    {/foreach}
{/foreach}
<script type='text/javascript'>
    {if ($ok)}
        $('#unit-test-harness-run-{$window_id}').attr('disabled',false);
    {/if}
</script>