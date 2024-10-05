{$system->cacheCheck()|json_encode}
{assign var=status value=$system->cacheCheck()}

{*
    "controllers":  "controller-<namspace/name>",
    "modules":      "module-<namespace>",
    "entities": {
        "keys": "entity_keys-<namespace/entity>",
        "cols": "entity_columns-<namespace/entity>"
    },
    "metadata":     ""
*}