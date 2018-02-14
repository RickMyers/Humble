{
    "active": {if ($system->isActive()==1)}true{else}false{/if},
    "quiescing": {if ($system->isQuiescing()==1)}true{else}false{/if}
}