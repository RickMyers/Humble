{assign var=method value=$comment->load(true)}
{if ($method.comment)}{$method.comment}{else}No information available{/if}