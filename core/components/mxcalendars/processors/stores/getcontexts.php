<?php
if (!$modx->user->isAuthenticated('mgr')) return $modx->error->failure($modx->lexicon('permission_denied'));

$contextKeys = array();
$query = $modx->newQuery('modContext', array('key:NOT IN' => array('mgr')));
$query->select($modx->getSelectColumns('modContext', 'modContext', '', array('key')));
if ($query->prepare() && $query->stmt->execute()) {
    $contextKeys = $query->stmt->fetchAll(PDO::FETCH_COLUMN);
}

/* iterate */
$list = array();
foreach ($contextKeys as $ctx) {
    $list[] = array('key'=>$ctx); 
}
return $this->outputArray($list,sizeof($list));