<?php

$mxcal = $modx->getService('mxcalendars','mxCalendars',$modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'model/mxcalendars/',$scriptProperties);
if (!($mxcal instanceof mxCalendars)) return 'Error loading instance of mxCalendars.';

include_once($modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'model/mxcalendars/mxcalendars.helper.class.php');

/* setup default properties */
$tpl = $modx->getOption('tpl',$scriptProperties,'list.row');
$sort = $modx->getOption('sort',$scriptProperties,'startdate');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
 
$output = '';

/*
 * Uncomment to create the table objects
 *

 $m = $modx->getManager();
 $created_calendar = $m->removeObjectContainer('mxCalendarEvents');
 $created_cats = $m->removeObjectContainer('mxCalendarCategories');
 $created_settings = $m->removeObjectContainer('mxCalendarSettings');
 $created_eventWUG = $m->removeObjectContainer('mxCalendarEventWUG');

 $created_calendar = $m->createObjectContainer('mxCalendarEvents');
 $created_cats = $m->createObjectContainer('mxCalendarCategories');
 $created_settings = $m->createObjectContainer('mxCalendarSettings');
 $created_eventWUG = $m->createObjectContainer('mxCalendarEventWUG');
*/
return ($created_calendar ? 'Table created calendar.' : 'Table not created.').
        ($created_cats ? 'Table created categories.' : 'Table not created categories.').
        ($created_settings ? 'Table created settings.' : 'Table not created settings.');


$mxcalendars = $modx->getCollection('mxCalendarEvents');
$output = "<br />Total Events: ".count($mxcalendars);

$c = $modx->newQuery('mxCalendarEvents');
$c->innerJoin('mxCalendarCategories','CategoryId');
$c->select(array(
	'mxCalendarEvents.*',
	'CategoryId.name', 	'CategoryId.foregroundcss', 'CategoryId.backgroundcss', 'CategoryId.inlinecss'
));
$c->sortby($sort,$dir);
//$c->where(array ('Zip.id' => $lookupZip,  ));

$output .= $c->toSQL();

$mxcalendars = $modx->getCollection('mxCalendarEvents',$c);
$output .= "<br />Returned Events: ".count($mxcalendars);

foreach ($mxcalendars as $mxc) {
    //-- Convert the object to an array
	$mxcArray = $mxc->toArray();
	
    //-- Split the single unix time stamp into date and time for UI
    $mxcArray['startdate_date'] = date('m-d-Y',$mxc->get('startdate'));  
    $mxcArray['startdate_time'] = date('h:i',$mxc->get('startdate'));
	
    //$output .= json_encode($mxcArray);
    //print_r($mxcArray);
    $output .= $mxcal->getChunk($tpl,$mxcArray);
}
/*
$modx->setPlaceholders(array(
   'eventList' => $output
),'mxc.');
*/

 
echo $mxcal->getChunk('list.wrap', array('eventList'=>$output));


echo '<h2>CATEGORIES TEST</h2>';
/* build query */
$c = $modx->newQuery('mxCalendarCategories');
//$c->innerJoin('modUser','CreatedBy');
//$c->innerJoin('modUser','EditedBy');
$c->select(array(
	'mxCalendarCategories.id',
	'mxCalendarCategories.name'
));
$c->where(array(
	'name:LIKE' => '%'.$query.'%',
	'disable' => 0,
	'active' => 1,
));
$c->sortby('name','ASC');
$mxcalendarsCats = $modx->getCollection('mxCalendarCategories', $c);
/* iterate */
$list = array();
foreach ($mxcalendarsCats as $mxc) {
    $list[] = $mxc->toArray();
}
print_r($list);


return '';