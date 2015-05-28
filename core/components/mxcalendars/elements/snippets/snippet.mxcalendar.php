<?php
/**
 * mxCalendar
 *
 * version: 1.1.10-pl
 *
 * @var $modx modX
 * @var $scriptProperties array
 */
/** @var $mxCal mxCalendars */
$mxCal = $modx->runSnippet('mxCalendarInit', $scriptProperties);

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
$labelCategoryHeading = $modx->getOption('labelCategoryHeading',$scriptProperties,$mxCal->modx->lexicon('mxcalendars.label_category_heading'));
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
// Defaults to blank (ie show all categories).
if ($categoryFilter = urldecode(isset($_REQUEST['cid']) ? $_REQUEST['cid'] : $modx->getOption('categoryFilter', $scriptProperties, null))) {
	// Adding comma for retrieving events that are category-agnostic (ie have blank category).
	$categoryFilter = ",{$categoryFilter}";
}
//++ Calendar Options (ver >= 1.1.0-pl)
// Defaults to blank (ie show all calendars).
if ($calendarFilter = (isset($_REQUEST['calf']) ? $_REQUEST['calf'] : $modx->getOption('calendarFilter', $scriptProperties, null))) {
	// Adding comma for retrieving events that are calendar-agnostic (ie have blank calendar).
	$calendarFilter = ",{$calendarFilter}";
};
//++ Context Options (ver >= 1.1.0-pl)
// Defaults to current context.
// Could be blank (ie show all contexts).
if ($contextFilter = ','.(isset($_REQUEST['conf']) ? $_REQUEST['conf'] : $modx->getOption('contextFilter',$scriptProperties, $modx->context->key))) {
	// Adding comma for retrieving events that are context-agnostic (ie have blank context).
	$contextFilter = ",{$contextFilter}";
}
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
if(!empty($setTimezone)) $mxCal->setTimeZone($setTimezone,$debugTimezone);
//-- Update to the Timezone: Manual fix to adjust timezone to match server settings examples.
//date_default_timezone_set("Europe/Amsterdam");
//date_default_timezone_set('America/New_York');
// Process any needed Feeds as setup in mxCalendar Manager

$feed = $modx->getObject('mxCalendarFeed', 10);
if($feed){
	$feed->set('nextrunon', 0);
	$feed-save();
}
$mxCal->processFeeds($setFeedTZ);
if($modx->resource->get('id') != $ajaxResourceId && $modx->resource->get('id') != $ajaxMonthResourceId) {
	//-- Add mxCalendar Theme CSS to html header (set in snippit properties)
	if($excludeCSS !== 1 && $excludeCSS !== '1'){
		$modx->regClientCSS($modx->getOption('mxcalendars.assets_url',null,$modx->getOption('assets_url').'components/mxcalendars/').'themes/'.$theme.'/css/mxcalendar.css');
	}
	//-- Add the Shadowbox library info if we are using modal
	if(($modalView == 'true' || $modalView == 1) && ($usemxcLib == 'true' || $usemxcLib == 1)) {
		$mxCal->addShadowBox($modalSetWidth,$modalSetHeight);
	} else { $mxCal->disableModal(); }
	//-- Add mxCalendar jQuery Library if enabled
	if($addJQ && $addJQ !== 'false'){
		$modx->regClientStartupScript($jqLibSrc);
		//-- Only add the required JS files we need
		if(!empty($ajaxResourceId) && $modx->resource->get('id') != $ajaxResourceId && $modx->resource->get('id') != $ajaxMonthResourceId)//-- Also requires a valid jQuery library be loaded
			$modx->regClientStartupScript($mxCal->config['assetsUrl'].'js/web/mxc-calendar.js');
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
if($debug) $output .= "<br />Total Events: ".count($modx->getCollection('mxCalendarEvents'));
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
		$dr = $mxCal->getEventCalendarDateRange($activeMonthOnlyEvents);
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
if (!empty($contextFilter)) {
	$whereArr['AND:context:IN'] = explode(',',$contextFilter);
}
if (!empty($calendarFilter)) {
	$whereArr['AND:calendar_id:IN'] = explode(',',$calendarFilter);
}
if (!empty($categoryFilter)) {
	foreach (explode(',',$categoryFilter) as $category) {
		if (empty($category)) {
			// For blank just show all.
			break;
		}
		if($displayType == 'calendar' || $displayType == 'mini' || $displayType == 'list') {
			$whereArr[] = array(
				array('categoryid' => $category),
				array('OR:categoryid:LIKE' => '%,'.$category.',%'),
				array('OR:categoryid:LIKE' => '%,'.$category),
				array('OR:categoryid:LIKE' => $category.',%'),
			);
		}
	}
}
$whereArr['mxCalendarEvents.active'] = 1;
$c->where($whereArr);
if($displayType != 'detail')
	$c->sortby($sort,$dir);
$c->limit($limit,$limitstart);

// Get events.
list($events, $debugOutputTemp) = $modx->runSnippet('mxCalendarGetEvents', array_merge($scriptProperties, array(
	'criteria' => $c,
	'debug' => $debug,
)));
$debugOutput .= $debugOutputTemp;

// Sort.
list($events, $debugOutputTemp) = $modx->runSnippet('mxCalendarSort', array_merge($scriptProperties, array(
	'events' => $events,
	'direction' => $dir,
	'debug' => $debug,
)));
$debugOutput .= $debugOutputTemp;

// Prepare for render.
list($events, $debugOutputTemp) = $modx->runSnippet('mxCalendarPrerender', array_merge($scriptProperties, array(
	'events' => $events,
	'elStartDate' => $elStartDate,
	'elEndDate' => $elEndDate,
	'displayType' => $displayType,
	'debug' => $debug,
	'limit' => $limit,
)));
$debugOutput .= $debugOutputTemp;

// @todo Refactor Avoid echoing from snippet.
echo $debugOutput;

// @todo Refactor Turn into render snippet.
$modx->setPlaceholders(array('dateseperator'=>$dateSeperator));
//----- NOW GET THE DISPLAY TYPE ------//
switch ($displayType){
	case 'list':
	case 'daily':
	case 'ical':
	case 'rss':
		$output = $mxCal->makeEventList($eventListLimit, $eventsArr, array('tplElItem'=>$tplElItem, 'tplElMonthHeading'=>$tplElMonthHeading, 'tplElWrap'=>$tplElWrap, 'tplImage'=>$tplImageItem, 'tplVideo'=>$tplVIdeoItem, 'tplNoEvents'=>$tplNoEvents),$elStartDate,$elEndDate);
		break;
	case 'year':
		break;
	case 'detail':
		if($debug) $output .= 'Total Occurances: '.count($eventsArr).' for Event ID: '.$_REQUEST['detail'].'<br />';
		if(isset($resourceId) && $modx->resource->get('id') != $resourceId)
			$tplDetail = $tplDetailModal;
		$output .= $mxCal->makeEventDetail($eventsArr,($occurance=$_REQUEST['r']?$_REQUEST['r']:0) , array('tplDetail'=>$tplDetail, 'tplImage'=>$tplImageItem, 'tplVideo'=>$tplVIdeoItem),$mapWidth,$mapHeight,$gmapRegion);
		//$whereArr[0]['AND:id:='] = (int)$_REQUEST['detail']; //@TODO Make filter for single events repeating dates
		break;
	case 'calendar':
	case 'mini':
	default:
		$timer_10 = new makeProcessTime($time_start,$debug);
		$output = $mxCal->makeEventCalendar($eventsArr,(!empty($ajaxResourceId) && $modalView? $ajaxResourceId : $resourceId),(!empty( $ajaxMonthResourceId) ?  $ajaxMonthResourceId : (!empty($ajaxResourceId) ? $ajaxResourceId : $resourceId) ),array('event'=>$tplEvent,'day'=>$tplDay,'week'=>$tplWeek,'month'=>$tplMonth,'heading'=>$tplHeading, 'tplImage'=>$tplImageItem, 'tplVideo'=>$tplVIdeoItem), $contextFilter, $calendarFilter, $highlightToday);
		$timer_10->end('UI Rendering');
		break;
}
//-- Always allow the category list placeholder to be set
if($showCategories == true)
	$modx->setPlaceholder('categories', $mxCal->makeCategoryList($labelCategoryHeading, ($_REQUEST['cid'] ? $_REQUEST['cid'] : null),$resourceId, array('tplCategoryWrap'=>$tplCategoryWrap, 'tplCategoryItem'=>$tplCategoryItem)));
$mxCal->restoreTimeZone($debugTimezone);
$time_end = microtime(true);
$time = $time_end - $time_start;
if($debug) echo "<br /><small>mxCalendar processed in $time seconds</small><br /><br />\n";
$modx->cacheManager->set($cacheElementKey, $output, $cacheExpires, $cacheOptions);

return $output;