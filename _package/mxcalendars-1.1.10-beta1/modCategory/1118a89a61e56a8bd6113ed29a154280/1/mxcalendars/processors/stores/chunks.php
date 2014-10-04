<?php

if (!$modx->user->isAuthenticated('mgr')) return $modx->error->failure($modx->lexicon('permission_denied'));

$query = $modx->getOption('query', $scriptProperties, '');

$chunks = $modx->getCollection('modChunk', array('name:LIKE' => '%'.$query.'%'));
        
$list = array();
foreach ($chunks as $chunk) {
    $list[] = $chunk->toArray();
}

return $this->outputArray($list, sizeof($list));