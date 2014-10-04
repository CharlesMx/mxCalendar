<?php

$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('mxCalendarCalendars');
if (!empty($query)) {
    $c->where(array(
        'name:LIKE' => '%'.$query.'%'
    ));
}
$count = $modx->getCount('mxCalendarCalendars',$c);
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$mxcalendarsCals = $modx->getIterator('mxCalendarCalendars', $c);

/* iterate */
$list = array();
foreach ($mxcalendarsCals as $mxc) {
    $mxcArray = $mxc->toArray();
    $list[]= $mxcArray;
}
return $this->outputArray($list,$count);

?>
