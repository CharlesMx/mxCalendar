<?php

$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'feed');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('mxCalendarFeed');
if (!empty($query)) {
    $c->where(array(
        'feed:LIKE' => '%'.$query.'%',
        'OR:type:LIKE' => '%'.$query.'%',
    ));
}
$count = $modx->getCount('mxCalendarFeed',$c);
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$mxresult = $modx->getIterator('mxCalendarFeed', $c);

/* iterate */
$list = array();
foreach ($mxresult as $mxc) {
    $mxcArray = $mxc->toArray();
    $list[]= $mxcArray;
}
return $this->outputArray($list,$count);

?>
