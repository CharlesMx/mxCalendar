<?php

if (!$modx->user->isAuthenticated('mgr')) return $modx->error->failure($modx->lexicon('permission_denied'));

$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('mxCalendarCalendars');

$c->select(array(
	'id',
	'name'
));

$c->where(array(
	'active' => 1,
));

$c->sortby('name','ASC');
$categories = $modx->getCollection('mxCalendarCalendars', $c);

/* iterate */
$list = array();
foreach ($categories as $mxc) {
    $list[] = $mxc->toArray();
}
return $this->outputArray($list,sizeof($list));



