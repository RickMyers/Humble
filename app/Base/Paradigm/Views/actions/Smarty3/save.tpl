{assign var=results value=$manager->saveDiagram()}
{if ($results && isset($results['$id']))}
{$results['$id']}
{/if}
