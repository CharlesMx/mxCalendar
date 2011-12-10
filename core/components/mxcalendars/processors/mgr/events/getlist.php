<?php

$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'startdate');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');
 
/*
$c = $xpdo->newQuery('Box');
$c->innerJoin('BoxOwner','Owner'); // arguments are: className, alias
$c->innerJoin('User','User','Owner.user = User.id');
// note the 3rd argument that defines the relationship in the innerJoin
 
$c->where(array(
   'Box.width' => 5,
   'User.user' => 2,
));
$c->sortby('Box.name','ASC');
$c->limit(5,5);
$boxes = $xpdo->getCollection('Box',$c);
*/ 
 
/* build query */
$c = $modx->newQuery('mxCalendarEvents');
$c->select(array(
	'mxCalendarEvents.*',
	'CategoryId.name', 	'CategoryId.foregroundcss', 'CategoryId.backgroundcss', 'CategoryId.inlinecss'
));
$c->innerJoin('mxCalendarCategories','CategoryId');
$c->innerJoin('categoryid','id','mxCalendarEvents.categoryid = CategoryId');
if (!empty($query)) {
    $c->where(array(
        'name:LIKE' => '%'.$query.'%',
        'OR:description:LIKE' => '%'.$query.'%',
    ));
}
$count = $modx->getCount('mxCalendarEvents',$c);
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$mxcalendars = $modx->getIterator('mxCalendarEvents', $c);
 
/* iterate */
$list = array();
foreach ($mxcalendars as $mxc) {
    $mxcArray = $mxc->toArray();
    //-- Split the single unix time stamp into date and time for UI
    $mxcArray['startdate_date'] = strftime('%m-%e-%Y',$mxc->get('startdate'));  
    $mxcArray['startdate_time'] = strftime('%I:%M %p',$mxc->get('startdate'));
	$mxcArray['startdate'] = strftime('%m-%e-%Y %I:%M %p',$mxc->get('startdate'));
	
	$mxcArray['enddate_date'] = strftime('%m-%e-%Y',$mxc->get('enddate'));  
    $mxcArray['enddate_time'] = strftime('%I:%M %p',$mxc->get('enddate'));
	$mxcArray['enddate'] = strftime('%m-%e-%Y %I:%M %p',$mxc->get('enddate'));
	$list[]= $mxcArray;
}
return $this->outputArray($list,$count);

?>
