<?php

$mxcal = $modx->getService('mxcalendars','mxCalendars',$modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'model/mxcalendars/',$scriptProperties);
if (!($mxcal instanceof mxCalendars)) return 'Error loading instance of mxCalendars.';

include_once($modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'model/mxcalendars/mxcalendars.helper.class.php');

/* setup default properties */
$theme = $modx->getOption('theme',$scriptProperties,'default');// default, traditional
$resourceId = $modx->getOption('detailId', $scriptProperties, $modx->resource->get('id'));
$displayType = isset($_REQUEST['detail']) ? 'detail' : $modx->getOption('displayType', $scriptProperties, 'calendar'); //calendar,list,mini
//++ Results query properties
$eventListStartDate = $modx->getOption('elStartDate',$scriptProperties,'now');
$eventListEndDate = $modx->getOption('elEndDate',$scriptProperties,'+1 year');
$tplElItem = $modx->getOption('tplListItem',$scriptProperties,'el.itemclean');
$tplElMonthHeading = $modx->getOption('tplListHeading',$scriptProperties,'el.listheading');
$tplElWrap = $modx->getOption('tplListWrap',$scriptProperties,'el.wrap');
$eventListLimit = $modx->getOption('eventListlimit',$scriptProperties,'5');
$sort = $modx->getOption('sort',$scriptProperties,'startdate');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$limit = $modx->getOption('limit',$scriptProperties,'99');
//++ Text|Date Formatting properties
$dateFormat = $modx->getOption('dateformat', $scriptProperties, '%Y-%m-%d');
$timeFormat = $modx->getOption('timeformat', $scriptProperties, '%H:%M %p');
$dateSeperator = $modx->getOption('dateseperator',$scriptProperties, '/');
//++ Display: Calendar properties
$activeMonthOnlyEvents = $modx->getOption('activeMonthOnlyEvents', $scriptProperties, false);
$hightlightToday = $modx->getOption('hightlightToday', $scriptProperties, true);
$todayClass = $modx->getOption('todayClass', $scriptProperties, 'today');
$noEventsClass = $modx->getOption('noEventClass', $scriptProperties, 'mxcDayNoEvents');
$hasEventsClass = $modx->getOption('hasEventsClass', $scriptProperties,'mxcEvents');
$tplEvent = $modx->getOption('tplEvent',$scriptProperties,'month.inner.container.row.day.eventclean');
$tplDay = $modx->getOption('tplDay',$scriptProperties,'month.inner.container.row.day');
$tplWeek = $modx->getOption('tplWeek',$scriptProperties,'month.inner.container.row');
$tplMonth = $modx->getOption('tplMonth',$scriptProperties,'month.inner.container');
$tplHeading = $modx->getOption('tplHeading',$scriptProperties,'month.inner.container.row.heading');
//++Display: Detail
$tplDetail = $modx->getOption('tplDetail',$scriptProperties,'detail');
//++ Used in very limited cases
$debug = $modx->getOption('debug',$scriptProperties,false);

// Lets check the timezone setting so that our date functions return correct dates for location
$mxcal->setTimeZone();


$elStartDate = strtotime($eventListStartDate);
if($elStartDate ===false){
    $elStartDate = time();
}
$elEndDate = strtotime($eventListEndDate);
if($elEndDate ===false){
    $elEndDate = time();
}


//-- Setup varibles to hold the output
$debugOutput = array();
$arrEventsDetail = array();
$arrEventDates=array();
$output = '';

$time_start = microtime(true);
$mxcalendars = $modx->getCollection('mxCalendarEvents');
if($debug) $output = "<br />Total Events: ".count($mxcalendars); else $output='';
$whereArr = array();
$eventsArr = array();

$c = $modx->newQuery('mxCalendarEvents');
$c->innerJoin('mxCalendarCategories','CategoryId');
$c->select(array(
	'mxCalendarEvents.*',
	'CategoryId.name AS category','CategoryId.foregroundcss','CategoryId.backgroundcss','CategoryId.inlinecss'
));
// Create the where clause by display type to limit the returned records
switch ($displayType){
    case 'list':
        $whereArr = array(array('repeating:=' => 0,'AND:enddate:>=' => $elStartDate,'AND:enddate:<=' => $elEndDate,array('OR:repeating:='=>1,'AND:repeatenddate:>=' => $elStartDate)) );
        break;
    case 'calendar':
    case 'mini':
    default:
        $dr = $mxcal->getEventCalendarDateRange($activeMonthOnlyEvents);
        $elStartDate = $dr['start'];
        $elEndDate = $dr['end'];
        $whereArr = array(array('repeating:=' => 0,'AND:enddate:>=' => $elStartDate,'AND:enddate:<=' => $elEndDate,array('OR:repeating:='=>1,'AND:repeatenddate:>=' => $elStartDate)) );//,'AND:repeatenddate:<=' => $dr['end']
        break;
    case 'detail':
        $whereArr = array('id:=' => (int)$_REQUEST['detail']);
        //$whereArr[0]['AND:id:='] = (int)$_REQUEST['detail']; //@TODO Make filter for single events repeating dates
        break;
}
$c->where($whereArr);
$c->sortby($sort,$dir);
$c->limit($limit,0);

$c->prepare();
if($debug) echo 'Filtering calendar date SQL range with: '.strftime('%m/%d/%Y', $elStartDate).' through '.strftime('%m/%d/%Y', $elEndDate).'<br /><br />';
if($debug) echo 'SQL: '.$c->toSql().'<br /><br />';

$mxcalendars = $modx->getCollection('mxCalendarEvents',$c);
if($debug) echo "<br />Returned Events: ".count($mxcalendars).'<br />';

//-- Add mxCalendar Theme CSS to html header (set in snippit properties)
$modx->regClientCSS($modx->getOption('mxcalendars.assets_url',null,$modx->getOption('assets_url').'components/mxcalendars/').'themes/'.$theme.'/css/mxcalendar.css');

foreach ($mxcalendars as $mxc) {
    //-- Convert the object to an array
    $mxcArray = $mxc->toArray();
	
    //-- Split the single unix time stamp into date and time for preformatted UI
    $mxcArray['startdate_fdate'] = strftime($dateFormat,$mxc->get('startdate'));  
    $mxcArray['startdate_ftime'] = strftime($timeFormat,$mxc->get('startdate'));
    $mxcArray['enddate_fdate'] = strftime($dateFormat,$mxc->get('enddate'));  
    $mxcArray['enddate_ftime'] = strftime($timeFormat,$mxc->get('enddate'));

    
    $eStart    = new DateTime(date('Y-m-d H:i:s',$mxc->get('startdate'))); 
    $eEnd      = new DateTime(date('Y-m-d H:i:s',$mxc->get('enddate')));
    
    $durYear; $durMonth; $durDay; $durHour; $durMin; $durSec;
    if(version_compare(PHP_VERSION, '5.3.0') >= 0){
        $diff = $eStart->diff($eEnd);
        $durYear = $diff->format('%y');
        $durMonth = $diff->format('%m');
        $durDay = $diff->format('%d');
        $durHour = $diff->format('%h');
        $durMin = $diff->format('%i');
        $durSec = $diff->format('%s');
    } else {
        $diff = (object)$mxcal->datediff($mxc->get('startdate'),$mxc->get('enddate'),true);
        $durYear = $diff->years;
        $durMonth = $diff->months;
        $durDay = $diff->days;
        $durHour = $diff->hours;
        $durMin = $diff->minutes;
        $durSec = $diff->seconds;
    }
    
    //-- return event duration values 
    $mxcArray['durYear']    = !empty($durYear) ? $durYear : null; 
    $mxcArray['durMonth']    = !empty($durMonth) ? $durMonth : null; 
    $mxcArray['durDay']      = !empty($durDay) ? $durDay : null; 
    $mxcArray['durHour']     = !empty($durHour) ? $durHour : null; 
    $mxcArray['durMin']      = !empty($durMin) ? $durMin : null; 
    $mxcArray['durSec']      = !empty($durSec) ? $durSec : null; 
    
    $arrEventsDetail[$mxcArray['id']] = $mxcArray;
    $arrEventDates[$mxcArray['id']] = array('date'=>$mxcArray['startdate'], 'eventId'=>$mxcArray['id'],'repeatId'=>0);
    
    //-- If we have repeating dates and repeating is enabled lets add those to the array
    if($mxcArray['repeating'] && count(explode(',', $mxcArray['repeatdates']))){
        if($debug) echo 'Repeating Event: '.$mxcArray['title'].'<br />';
        if($debug) echo '&nbsp;&nbsp;&nbsp;++(0)&nbsp;&nbsp;'.strftime($dateFormat.' '.$timeFormat, $mxcArray['startdate']).'<br>';
        $rid = 1;
        foreach(explode(',',$mxcArray['repeatdates']) AS $rDate){
            $arrEventDates[$mxcArray['id'].'-'.$rid] = array('date'=>$rDate, 'eventId'=>$mxcArray['id'],'repeatId'=>$rid);
            if($debug) echo '&nbsp;&nbsp;&nbsp;++('.$rid.')&nbsp;&nbsp;'.strftime($dateFormat.' '.$timeFormat, $rDate).'<br>';
            $rid++;
        }
    }
   
    //$output .= $mxcal->getChunk($tpl,$mxcArray);
}

usort($arrEventDates, "custom_sort");
function custom_sort($a,$b){
    return $a['date']>$b['date'];
}
if(count($arrEventDates)){
    if($debug) echo 'Looping through events list of '.count($arrEventDates).' total.<br />';
    $ulimit=0;
    foreach($arrEventDates AS $e){
        
            $oDetails = $arrEventsDetail[$e['eventId']]; //Get original event (parent) details
            $oDetails['startdate'] = $e['date'];
            $oDetails['enddate'] = strtotime('+'.($arrEventsDetail[$e['eventId']]['durDay'] ? $arrEventsDetail[$e['eventId']]['durDay'].' days ' :'').($arrEventsDetail[$e['eventId']]['durHour'] ? $arrEventsDetail[$e['eventId']]['durHour'].' hour ' :'').($arrEventsDetail[$e['eventId']]['durMin'] ? $arrEventsDetail[$e['eventId']]['durMin'].' minute' :''), $e['date']);//$e['date'];//repeatenddate
            //if( (($oDetails['startdate']   >= $elStartDate || $oDetails['enddate'] >= $elStartDate)&& $oDetails['enddate']<=$elEndDate ) ){
            $oDetails['startdate_fdate'] = strftime($dateFormat,$oDetails['startdate']);  
            $oDetails['startdate_ftime'] = strftime($timeFormat,$oDetails['startdate']);
            $oDetails['enddate_fdate'] = strftime($dateFormat,$oDetails['enddate']);  
            $oDetails['enddate_ftime'] = strftime($timeFormat,$oDetails['enddate']);
            $oDetails['detailURL'] = $modx->makeUrl($resourceId,'',array('detail' => $e['eventId'], 'r'=>$e['repeatId']));
            $eventsArr[strftime('%Y-%m-%d', $e['date'])][] = $oDetails;
            $ulimit++;
            if($debug) echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$ulimit.') '.strftime($dateFormat,$e['date']).' '.$e['eventId'].'<br />';
            if($ulimit >= $limit && $displayType=='list' ) break;
            //}
    }
} else {
    if($debug) echo 'No valid dates returned.';
}

$modx->setPlaceholders(array('dateseperator'=>$dateSeperator));

//----- NOW GET THE DISPLAY TYPE ------//
// Create the where clause by display type to limit the returned records
switch ($displayType){
    case 'list':
        $output = $mxcal->makeEventList($eventListLimit, $eventsArr, array('tplElItem'=>$tplElItem, 'tplElMonthHeading'=>$tplElMonthHeading, 'tplElWrap'=>$tplElWrap));
        break;
    case 'calendar':
    case 'mini':
    default:
        $output = $mxcal->makeEventCalendar($eventsArr,$resourceId,array('event'=>$tplEvent,'day'=>$tplDay,'week'=>$tplWeek,'month'=>$tplMonth,'heading'=>$tplHeading));
        break;
    case 'detail':
        $output .= 'Total Occurances: '.count($eventsArr).'<br />';
        $output .= $mxcal->makeEventDetail($eventsArr,($occurance=$_REQUEST['r']?$_REQUEST['r']:0) , array('tplDetail'=>$tplDetail));
        //$whereArr = array('id:=' => (int)$_REQUEST['detail']);
        //$whereArr[0]['AND:id:='] = (int)$_REQUEST['detail']; //@TODO Make filter for single events repeating dates
        break;
}

/*
$output .= '<h2>CATEGORIES LIST TEST</h2>';
// build category query
$c = $modx->newQuery('mxCalendarCategories');
$c->where(array(
	'name:LIKE' => '%'.$query.'%',
	'disable' => 0,
	'active' => 1,
));
$c->sortby('name','ASC');
$mxcalendarsCats = $modx->getCollection('mxCalendarCategories', $c);
// iterate
$list = array();
foreach ($mxcalendarsCats as $mxc) {
    $list[] = $mxc->toArray();
}
$output .= json_encode($list);
*/

// Now we can set the timezone back if we changed it
$mxcal->restoreTimeZone();

$time_end = microtime(true);
$time = $time_end - $time_start;
if($debug) echo "<br /><small>mxCalendar processed in $time seconds</small><br /><br />\n";
return $output;