<?php

require "Humble.php";

print(\Environment::project('namespace')."\n");

$ent = Humble::entity('admin/users');

foreach ($ent->listEntities() as $entity) {
    print($entity."\n");
}