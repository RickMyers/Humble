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
        {assign var=results value=$unit_tests->fetchTestResult($package,$test->class)}
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
                {if (isset($results.coverage))}{$results.coverage} {$results.status}{else}N/A{/if}
            </div>
            <div style='background-color: rgba(202,202,202,.2); padding: 5px 2px; display: inline-block; min-width: 100px; width: 10%; margin-right: 2px; text-align: center'>
                <div style='background-color: {if ($results.score >= 2)}red{else}transparent{/if}; width: 6px; height: 8px; border: 1px solid #333; display: inline-block; margin-right: 3px; margin-top: 1px'>

                </div>
                <div style='background-color: {if ($results.score == 1)}yellow{else}transparent{/if}; width: 6px; height: 8px; border: 1px solid #333; display: inline-block; margin-right: 3px; margin-top: 1px'>

                </div>
                <div style='background-color: {if ($results.score == 0)}lightgreen{else}transparent{/if}; width: 6px; height: 8px; border: 1px solid #333; display: inline-block; margin-right: 3px; margin-top: 1px'>

                </div>
            </div>
        </div>
    {/foreach}
{/foreach}