<?php
$eventId = $modx->getOption('eventid',$scriptProperties,0);
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = 3;//$modx->getOption('limit',$scriptProperties,5);
$sort = $modx->getOption('sort',$scriptProperties,'title');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('mxCalendarEventVideos');
$c->where(array('event_id'=>$eventId));
if (!empty($query)) {
    $c->where(array(
        'title:LIKE' => '%'.$query.'%'
        ,'OR:description:LIKE' => '%'.$query.'%'
    ));
}



$count = $modx->getCount('mxCalendarEventVideos',$c);
$c->sortby($sort,$dir);
$c->limit($limit,$start);
$mxcalendarsVideos = $modx->getIterator('mxCalendarEventVideos', $c);

/* iterate */
$list = array();
foreach ($mxcalendarsVideos as $mxc) {
    $mxcArray = $mxc->toArray();
    $list[]= $mxcArray;
}
return $this->outputArray($list,$count);

?>
