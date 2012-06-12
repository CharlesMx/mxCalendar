<?php

$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'startdate');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');
$historical = $modx->getOption('historical',$scriptProperties,0);
 
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
        'title:LIKE' => '%'.$query.'%',
        'OR:description:LIKE' => '%'.$query.'%',
        'OR:CategoryId.name:LIKE'=>'%'.$query.'%',
    ));
} else {
    if($historical){
        $c->where(array(
            'repeating:=' => 0
            ,'AND:enddate:<=' => time()
            ,array(
                'OR:repeating:='=>1
                ,'AND:repeatenddate:<=' => time()
            )
        ));
    } else {
        $c->where(array(
            'repeating:=' => 0
            ,'AND:enddate:>=' => time()
            ,array(
                'OR:repeating:='=>1
                ,'AND:repeatenddate:>=' => time()
            )
        ));
    }
}
$count = $modx->getCount('mxCalendarEvents',$c);
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$mxcalendars = $modx->getIterator('mxCalendarEvents', $c);
 
//-- Get Settings Date and Time formats
$dateFormat = $modx->getOption('mxcalendars.mgr_dateformat', '', 'm/d/Y');
$timeFormat = $modx->getOption('mxcalendars.mgr_timeformat', '', 'g:i a');

/* iterate */
$list = array();
foreach ($mxcalendars as $mxc) {
    $mxcArray = $mxc->toArray();
    //-- Split the single unix time stamp into date and time for UI
    $mxcArray['startdate_date'] = date($dateFormat,$mxc->get('startdate'));  
    $mxcArray['startdate_time'] = date($timeFormat,$mxc->get('startdate'));
    $mxcArray['startdate'] = $mxc->get('startdate');

    $mxcArray['enddate_date'] = date($dateFormat,$mxc->get('enddate'));  
    $mxcArray['enddate_time'] = date($timeFormat,$mxc->get('enddate'));
    $mxcArray['enddate'] = $mxc->get('enddate');

    $ed = $mxc->get('repeatenddate');
    $mxcArray['repeatenddate'] = !empty($ed) ? $mxc->get('repeatenddate') : null;  
    $list[]= $mxcArray;
}
return $this->outputArray($list,$count);

?>
