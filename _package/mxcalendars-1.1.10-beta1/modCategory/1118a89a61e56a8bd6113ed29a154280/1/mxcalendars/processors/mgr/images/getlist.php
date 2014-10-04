<?php
$eventId = $modx->getOption('eventid',$scriptProperties,0);
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = 3;//$modx->getOption('limit',$scriptProperties,5);
$sort = $modx->getOption('sort',$scriptProperties,'title');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('mxCalendarEventImages');
$c->where(array('event_id'=>$eventId));
if (!empty($query)) {
    $c->where(array(
        'title:LIKE' => '%'.$query.'%'
        ,'OR:description:LIKE' => '%'.$query.'%'
    ));
}



$count = $modx->getCount('mxCalendarEventImages',$c);
$c->sortby($sort,$dir);
$c->limit($limit,$start);
$mxcalendarsImages = $modx->getIterator('mxCalendarEventImages', $c);

/* iterate */
$list = array();
foreach ($mxcalendarsImages as $mxc) {
    $mxcArray = $mxc->toArray();
    $list[]= $mxcArray;
}
return $this->outputArray($list,$count);

?>
