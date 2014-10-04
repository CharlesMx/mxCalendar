<?php
/**
 * mxCalendar 
 * 
 * version: 1.1.8-pl
 *  
 */

$mxcal = $modx->getService('mxcalendars','mxCalendars',$modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'model/mxcalendars/',$scriptProperties);
if (!($mxcal instanceof mxCalendars)) return 'Error loading instance of mxCalendars.';

include_once($modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'processors/mgr/mxcHelper.php');

/* setup default properties */
$theme = $modx->getOption('theme',$scriptProperties,'default');// default, traditional
$excludeCSS = $modx->getOption('excludeCSS', $scriptProperties, 0);
$resourceId = $modx->getOption('resourceId', $scriptProperties, $modx->resource->get('id'));
$isLocked = $modx->getOption('isLocked', $scriptProperties, 0);
$displayType = isset($_REQUEST['detail']) && !$isLocked ? 'detail' : (isset($_REQUEST['displayType']) ? $_REQUEST['displayType'] : $modx->getOption('displayType', $scriptProperties, 'calendar')); //calendar,list,mini
$eventid = !empty($_REQUEST['mxcid']) ? (int)$_REQUEST['mxcid'] : null;
//++ Images properties
$imageLimit = $modx->getOption('limit',$scriptProperties,'15');
$imageDisable = $modx->getOption('imageDisable',$scriptProperties,0);
//++ Results query properties
$eventListStartDate = (isset($_REQUEST['elStartDate']) && !$isLocked ? $_REQUEST['elStartDate'] : $modx->getOption('elStartDate',$scriptProperties,'now'));
$eventListEndDate = (isset($_REQUEST['elEndDate']) && !$isLocked ? $_REQUEST['elEndDate'] : $modx->getOption('elEndDate',$scriptProperties,'+1 year'));
$elDirectional = $modx->getOption('elDirectional',$scriptProperties, false);
$tplElItem = $modx->getOption('tplListItem',$scriptProperties,'el.itemclean');
$tplElMonthHeading = $modx->getOption('tplListHeading',$scriptProperties,'el.listheading');
$tplElWrap = $modx->getOption('tplListWrap',$scriptProperties,'el.wrap');
$tplNoEvents = $modx->getOption('tplNoEvents',$scriptProperties,'el.noevents');
$eventListLimit = $modx->getOption('eventListlimit',$scriptProperties,'5');
$sort = $modx->getOption('mxc.sort',$scriptProperties,'startdate');
$dir = $modx->getOption('mxc.dir',$scriptProperties,'ASC');
$limit = $modx->getOption('limit',$scriptProperties,'99');
$limitstart = $modx->getOption('limitstart', $scriptProperties, 0);
//++ Text|Date Formatting properties
$dateFormat = $modx->getOption('dateformat', $scriptProperties, '%Y-%m-%d');
$timeFormat = $modx->getOption('timeformat', $scriptProperties, '%H:%M %p');
$dateSeperator = $modx->getOption('dateseperator',$scriptProperties, '/');
//++ Display: Calendar properties
$activeMonthOnlyEvents = $modx->getOption('activeMonthOnlyEvents', $scriptProperties, 0);
$highlightToday = $modx->getOption('highlightToday', $scriptProperties, 1);
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
$tplDetailModal = $modx->getOption('tplDetailModal', $scriptProperties, 'detail.modal');
$tplImageItem = $modx->getOption('tplImageItem', $scriptProperties, 'image');
$tplVideoItem = $modx->getOption('tplVideoItem', $scriptProperties, 'video');
$mapWidth = $modx->getOption('mapWidth', $scriptProperties, '500px');
$mapHeight = $modx->getOption('mapHeight', $scriptProperties, '500px');
//++Display: Categories
$showCategories = $modx->getOption('showCategories',$scriptProperties,1);
$tplCategoryWrap = $modx->getOption('tplCategoryWrap',$scriptProperties,'category.container');
$tplCategoryItem = $modx->getOption('tplCategoryItem',$scriptProperties,'category.container.item');
$labelCategoryHeading = $modx->getOption('labelCategoryHeading',$scriptProperties,$mxcal->modx->lexicon('mxcalendars.label_category_heading'));
//@TODO Possibly add to the properties set
//++Aux Parameters: AJAX, Modal, etc.*
$addJQ = $modx->getOption('addJQ', $scriptProperties,1); //-- jQuery is required for the core mxCalendar JS to function
$jqLibSrc = $modx->getOption('jqLibSrc', $scriptProperties,'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
$usemxcLib = $modx->getOption('usemxcLib', $scriptProperties,1); //-- Use the stand-a-lone modal windows JS library packaged with mxCalendar
$ajaxResourceId = $modx->getOption('ajaxResourceId', $scriptProperties, null);
$ajaxMonthResourceId =  $modx->getOption('ajaxMonthResourceId', $scriptProperties, null);
$modalView = $modx->getOption('modalView', $scriptProperties,1);
$modalSetWidth = $modx->getOption('modalSetWidth', $scriptProperties,null); //-- Ver > 0.0.3-beta
$modalSetHeight =$modx->getOption('modalSetHeight', $scriptProperties,null); //-- Ver > 0.0.3-beta
//@TODO Possibly add to the properties set
//++Location Specific options for Google Maps v3.x
$gmapLib = $modx->getOption('gmapLib', $scriptProperties, 'http://maps.google.com/maps/api/js?sensor=false');
$gmapId = $modx->getOption('gmapId',$scriptProperties, 'map');
$gmapDefaultZoom = $modx->getOption('gmapDefaultZoom', $scriptProperties, '13');
$gmapAPIKey = $modx->getOption('gmapAPIKey', $scriptProperties, 'null');
$gmapRegion = $modx->getOption('gmapRegion', $scriptProperties, '');
//++ Holiday Support
$holidays = $modx->getOption('holidays', $scriptProperties, "{'us':{''}}");
$holidayDisplayEvents = $modx->getOption('holidayDisplayEvents', $scriptProperties, 1);
//++ Used in very limited cases
$setTimezone = $modx->getOption('setTimezone', $scriptProperties, null );
$debugTimezone = $modx->getOption('debugTimezone', $scriptProperties, 0 );
$debug = $modx->getOption('debug',$scriptProperties,0);
//++ Set a feed processor timezone adjustment
$setFeedTZ = $modx->getOption('setFeedTZ', $scriptProperties, null); // Example: FeedId=2; TargetTimeZone=New York; would result in =>  `{"2":"America/New_York"}`

//++ Calendar Options (ver >= 1.1.6d-pr)
$categoryFilter = urldecode(isset($_REQUEST['cid']) ? $_REQUEST['cid'] : $modx->getOption('categoryFilter', $scriptProperties, null)); //-- Defaults to show all categories
//++ Calendar Options (ver >= 1.1.0-pl)
$calendarFilter = isset($_REQUEST['calf']) ? $_REQUEST['calf'] : $modx->getOption('calendarFilter', $scriptProperties, null); //-- Defaults to show all calendars
//++ Context Options (ver >= 1.1.0-pl)
$contextFilter = isset($_REQUEST['conf']) ? $_REQUEST['conf'] : $modx->getOption('contextFilter',$scriptProperties, ','.$modx->context->key);//-- Defaults to current context + (blank for all)
//++ Form Chunk Filter match name
$formFilter = $modx->getOption('formFilter',$scriptProperties,'form_');

//++ Caching Options
$cacheEnable =  $modx->getOption('cacheEnable',$scriptProperties,0);
$cacheLifetime = $modx->getOption('cacheLifetime',$scriptProperties,null); //3600 would be one hour - and overrides the resource lifetime; leaving null inherits the resource cache lifetime settings
if (empty($cacheKey)) $cacheKey = $modx->getOption('cache_resource_key', null, 'resource');
if (empty($cacheHandler)) $cacheHandler = $modx->getOption('cache_resource_handler', null, $modx->getOption(xPDO::OPT_CACHE_HANDLER, null, 'xPDOFileCache'));
if (!isset($cacheExpires)) $cacheExpires = $cacheLifetime!==null ? (integer)$cacheLifetime : (integer) $modx->getOption('cache_resource_expires', null, $modx->getOption(xPDO::OPT_CACHE_EXPIRES, null, 0));
if (empty($cacheElementKey)) $cacheElementKey = $modx->resource->getCacheKey() . '/' . md5($modx->toJSON($properties) . implode('', $modx->request->getParameters()));
$cacheOptions = array(xPDO::OPT_CACHE_KEY => $cacheKey,xPDO::OPT_CACHE_HANDLER => $cacheHandler,xPDO::OPT_CACHE_EXPIRES => $cacheExpires,);
$results = $modx->cacheManager->get($cacheElementKey, $cacheOptions);

 
//-- Update to the Timezone
 if(!empty($setTimezone)) $mxcal->setTimeZone($setTimezone,$debugTimezone);
//-- Update to the Timezone: Manual fix to adjust timezone to match server settings examples.
//date_default_timezone_set("Europe/Amsterdam");
//date_default_timezone_set('America/New_York');

// Process any needed Feeds as setup in mxCalendar Manager
 
 $feed = $modx->getObject('mxCalendarFeed', 10);
 if($feed){
     $feed->set('nextrunon', 0);
     $feed-save();
 }
$mxcal->processFeeds($setFeedTZ);


if($modx->resource->get('id') != $ajaxResourceId && $modx->resource->get('id') != $ajaxMonthResourceId) {
    //-- Add mxCalendar Theme CSS to html header (set in snippit properties)
    if($excludeCSS !== 1 && $excludeCSS !== '1'){
        $modx->regClientCSS($modx->getOption('mxcalendars.assets_url',null,$modx->getOption('assets_url').'components/mxcalendars/').'themes/'.$theme.'/css/mxcalendar.css');
    }

    //-- Add the Shadowbox library info if we are using modal
    if(($modalView == 'true' || $modalView == 1) && ($usemxcLib == 'true' || $usemxcLib == 1)) {
        $mxcal->addShadowBox($modalSetWidth,$modalSetHeight);
    } else { $mxcal->disableModal(); }

    //-- Add mxCalendar jQuery Library if enabled
    if($addJQ && $addJQ !== 'false'){
        $modx->regClientStartupScript($jqLibSrc);
    //-- Only add the required JS files we need
    if(!empty($ajaxResourceId) && $modx->resource->get('id') != $ajaxResourceId && $modx->resource->get('id') != $ajaxMonthResourceId)//-- Also requires a valid jQuery library be loaded
        $modx->regClientStartupScript($mxcal->config['assetsUrl'].'js/web/mxc-calendar.js');
    }
}


if(((int)$cacheEnable === 1 || $cacheEnable === '1') && !empty($results) && $debug !== 1)
    return $results;
           
if($debug)
var_dump($scriptProperties);

$elStartDate = strtotime($eventListStartDate);
if($elStartDate ===false){
    if($debug) echo 'Could not convert <strong>elStartDate</strong> value of "'.$elStartDate.'" to proper time stamp.<br />';
    $elStartDate = time();

}
$elEndDate = strtotime($eventListEndDate);
if($elEndDate ===false){
    if($debug) echo 'Could not convert <strong>elEndDate</strong> value of "'.$elEndDate.'" to proper time stamp.<br />';
    $elEndDate = time();
}

            
//-- Setup varibles to hold the output
$debugOutput = array();
$arrEventsDetail = array();
$arrEventDates=array();
$output = '';

$time_start = microtime(true);
$mxcalendars = $modx->getCollection('mxCalendarEvents');
if($debug) $output .= "<br />Total Events: ".count($mxcalendars);
$whereArr = array();
$eventsArr = array();

$c = $modx->newQuery('mxCalendarEvents');
$c->select(array(
	'mxCalendarEvents.*',
));
// Create the where clause by display type to limit the returned records
switch ($displayType){
    case 'list':
    case 'daily':
    case 'ical':
    case 'rss':
        $sort = 'startdate';
        if(is_integer($eventid) && $eventid !== null){
            $whereArr = array(array('mxCalendarEvents.id:='=>$eventid));
        } elseif (!$elDirectional) {
            $whereArr = array(array('repeating:=' => 0,'AND:enddate:>=' => $elStartDate,'AND:enddate:<=' => $elEndDate,array('OR:repeating:='=>1,'AND:repeatenddate:>=' => $elStartDate)) );
        } else {
            SWITCH($elDirectional){
                default:
                case 'f':
                case 'future':
                case 'forward':
                    $whereArr = array(array('repeating:=' => 0,array('AND:enddate:>=' => $elStartDate,'OR:enddate:>=' => $elStartDate),array('OR:repeating:='=>1,'AND:repeatenddate:>=' => $elStartDate)) );
                    break;
                case 'b':
                case 'p':
                case 'past':
                case 'backward':
                    $whereArr = array(array('repeating:=' => 0,array('AND:enddate:<=' => $elStartDate,'OR:enddate:<=' => $elStartDate),array('OR:repeating:='=>1,'AND:repeatenddate:<=' => $elStartDate)) );
                    break;
            }
        }
        break;
    case 'calendar':
    case 'mini':
    default:
        $dr = $mxcal->getEventCalendarDateRange($activeMonthOnlyEvents);
        $elStartDate = $dr['start'];
        $elEndDate = $dr['end'];
        $whereArr = array(array(
                        'repeating:=' => 0,
                        'AND:enddate:>=' => $elStartDate,
                        'AND:enddate:<=' => $elEndDate
                        ,array('OR:repeating:='=>1,
                                'AND:repeatenddate:>=' => $elStartDate)
                    ) );
        break;
    case 'year':
        break;
    case 'detail':
        $whereArr = array(array('id' => (int)$_REQUEST['detail']));
        //$whereArr[0]['AND:id:='] = (int)$_REQUEST['detail']; //@TODO Make filter for single events repeating dates
        break;
}

//-- ADD IN THE CONTEXT AND CALENDAR PROPERTY FILTERS
$whereArr['AND:context:IN'] = explode(',',$contextFilter);
if(!empty($calendarFilter))
    $whereArr['AND:calendar_id:IN'] = explode(',',$calendarFilter);

                        
if($categoryFilter && ($displayType == 'calendar' || $displayType == 'mini' || $displayType == 'list'))
        $whereArr[] = array(
            array('categoryid' => $categoryFilter),
            array('OR:categoryid:LIKE' => '%,'.$categoryFilter.',%'),
            array('OR:categoryid:LIKE' => '%,'.$categoryFilter),
            array('OR:categoryid:LIKE' => $categoryFilter.',%'),
            );

$whereArr['mxCalendarEvents.active'] = 1;

$c->where($whereArr);
if($displayType != 'detail')
    $c->sortby($sort,$dir);
$c->limit($limit,$limitstart);

$c->prepare();
if($debug) echo '<br /><br />Filtering calendar date SQL range with: '.strftime('%m/%d/%Y', $elStartDate).' through '.strftime('%m/%d/%Y', $elEndDate).'<br /><br />';
if($debug) echo 'SQL: '.$c->toSql().'<br /><br />';

$queryTimer = new makeProcessTime($time_start,$debug);
$mxcalendars = $modx->getCollection('mxCalendarEvents',$c);
$queryTimer->end('mxCalendars Query');

if($debug) echo "<br />Returned Events: ".count($mxcalendars).'<br />';


$resultLoopTimer = new makeProcessTime($time_start,$debug);
foreach ($mxcalendars as $mxc) {
    //-- Convert the object to an array
    $mxcArray = $mxc->toArray();
   
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
    $mxcArray['mxcmodalClass'] = ($modalView && $ajaxResourceId || isset($_REQUEST['imajax']) ? 'mxcmodal' : '');
    
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
    
    // Add date for each day in a multiple day event ( #127 )
    if($mxcArray['durDay'] !== null && $mxcArray['durDay'] >= 1){
        if($debug) echo 'Multiple Day Event: '.$mxcArray['title'].'<br />';
        for($i = 1; $i<=$mxcArray['durDay']; $i++){
            $theSpanDate = strtotime('+'.$i.' day', $mxcArray['startdate']);
            $arrEventDates[$mxcArray['id'].'_'.$i] = array('date'=>$theSpanDate, 'eventId'=>$mxcArray['id'],'repeatId'=>null);
            if($debug) echo '&nbsp;&nbsp;&nbsp;++(0)&nbsp;&nbsp;'.strftime($dateFormat.' '.$timeFormat, $theSpanDate).'<br>';
        }
    }
   
    //$output .= $mxcal->getChunk($tpl,$mxcArray);
}
$resultLoopTimer->end('mxc result set loop');


// Obtain a list of columns
$timer_5 = new makeProcessTime($time_start,$debug);
foreach ($arrEventDates as $key => $row) {
    $date[$key]  = $row['date'];
    $event[$key] = $row['eventId'];
}
$timer_5->end('generate list of all columns for sorting');

// Sort the data with volume descending, edition ascending
// Add $data as the last parameter, to sort by the common key
if(count($arrEventDates) && $displayType == 'list'){
    $multiSortTimer = new makeProcessTime($time_start,$debug);
    if($dir == 'ASC')
        array_multisort($date, SORT_ASC, $event, SORT_ASC, $arrEventDates);
    else
        array_multisort($date, SORT_DESC, $event, SORT_DESC, $arrEventDates);
    $multiSortTimer->end('array_multisort');
} else {
    //array_multisort($date, SORT_ASC, $event, SORT_ASC, $arrEventDates);
}

if(count($arrEventDates)){
    if($debug) echo 'Looping through events list of '.count($arrEventDates).' total.<br />';
    $ulimit=0;
    
    $arraEventTimer = new makeProcessTime($time_start,$debug);
        
    foreach($arrEventDates AS $k=>$e){
        
            $oDetails = $arrEventsDetail[$e['eventId']]; //Get original event (parent) details
            $oDetails['startdate'] = $e['date'];
            $oDetails['enddate'] = strtotime('+'.($arrEventsDetail[$e['eventId']]['durDay'] ? $arrEventsDetail[$e['eventId']]['durDay'].' days ' :'').($arrEventsDetail[$e['eventId']]['durHour'] ? $arrEventsDetail[$e['eventId']]['durHour'].' hour ' :'').($arrEventsDetail[$e['eventId']]['durMin'] ? $arrEventsDetail[$e['eventId']]['durMin'].' minute' :''), $e['date']);//$e['date'];//repeatenddate
            if(( ( ($oDetails['startdate']>=$elStartDate || $oDetails['enddate'] >= $elStartDate) && $oDetails['enddate']<=$elEndDate) || $displayType=='detail' || $elDirectional ) ){

                
                $oDetails['startdate_fstamp'] = $e['date']; 
                $oDetails['enddate_fstamp'] = $arrEventsDetail[$e['eventId']]['enddate'];
                
                $oDetails['detailURL'] = $modx->makeUrl((!empty($ajaxResourceId) && (bool)$modalView === true ? $ajaxResourceId : $resourceId),'',array('detail' => $e['eventId'], 'r'=>$e['repeatId']));
                $eventsArr[strftime('%Y-%m-%d', $e['date'])][] = $oDetails;
                $ulimit++;
                if($debug) echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$ulimit.'['.$limit.']) '.strftime($dateFormat,$e['date']).' '.$e['eventId'].'<br />';
                if($ulimit >= $limit && $displayType=='list' ) break;
            }
    }

    $arraEventTimer->end('arrEventDates');
        
} else {
    if($debug) echo 'No valid dates returned.';
}

$modx->setPlaceholders(array('dateseperator'=>$dateSeperator));

//----- NOW GET THE DISPLAY TYPE ------//
switch ($displayType){
    case 'list':
    case 'daily':
    case 'ical':
    case 'rss':
        $output = $mxcal->makeEventList($eventListLimit, $eventsArr, array('tplElItem'=>$tplElItem, 'tplElMonthHeading'=>$tplElMonthHeading, 'tplElWrap'=>$tplElWrap, 'tplImage'=>$tplImageItem, 'tplVideo'=>$tplVIdeoItem, 'tplNoEvents'=>$tplNoEvents),$elStartDate,$elEndDate);
        break;
    case 'calendar':
    case 'mini':
    default:
        $timer_10 = new makeProcessTime($time_start,$debug);
        $output = $mxcal->makeEventCalendar($eventsArr,(!empty($ajaxResourceId) && $modalView? $ajaxResourceId : $resourceId),(!empty( $ajaxMonthResourceId) ?  $ajaxMonthResourceId : (!empty($ajaxResourceId) ? $ajaxResourceId : $resourceId) ),array('event'=>$tplEvent,'day'=>$tplDay,'week'=>$tplWeek,'month'=>$tplMonth,'heading'=>$tplHeading, 'tplImage'=>$tplImageItem, 'tplVideo'=>$tplVIdeoItem), $contextFilter, $calendarFilter, $highlightToday);
        $timer_10->end('UI Rendering');
        break;
    case 'year':
        break;
    case 'detail':
        if($debug) $output .= 'Total Occurances: '.count($eventsArr).' for Event ID: '.$_REQUEST['detail'].'<br />';
        if(isset($resourceId) && $modx->resource->get('id') != $resourceId)
                $tplDetail = $tplDetailModal;
        $output .= $mxcal->makeEventDetail($eventsArr,($occurance=$_REQUEST['r']?$_REQUEST['r']:0) , array('tplDetail'=>$tplDetail, 'tplImage'=>$tplImageItem, 'tplVideo'=>$tplVIdeoItem),$mapWidth,$mapHeight,$gmapRegion);
        //$whereArr[0]['AND:id:='] = (int)$_REQUEST['detail']; //@TODO Make filter for single events repeating dates
        break;
}

//-- Always allow the category list placeholder to be set
if($showCategories == true)
    $modx->setPlaceholder('categories', $mxcal->makeCategoryList($labelCategoryHeading, ($_REQUEST['cid'] ? $_REQUEST['cid'] : null),$resourceId, array('tplCategoryWrap'=>$tplCategoryWrap, 'tplCategoryItem'=>$tplCategoryItem)));

$mxcal->restoreTimeZone($debugTimezone);
$time_end = microtime(true);
$time = $time_end - $time_start;
if($debug) echo "<br /><small>mxCalendar processed in $time seconds</small><br /><br />\n";


$modx->cacheManager->set($cacheElementKey, $output, $cacheExpires, $cacheOptions);

return $output;