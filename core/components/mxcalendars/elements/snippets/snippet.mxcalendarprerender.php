<?php
/**
 * mxCalendar prepare for render.
 *
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @var $events array array of events to prepare
 * @var $elStartDate int event list start date timestamp
 * @var $elEndDate int event list end date timestamp
 * @var $displayType string
 * @var $elDirectional bool
 * @var $limit int
 * @var $debug bool
 *
 * @return array(
 *   'events',      // sorted array of events
 *   'debugOutput', // string, containing information, valuable for debugging
 */
$mxCal = $modx->runSnippet('mxCalendarInit', $scriptProperties);

$debugOutput = '';
$debug = $modx->getOption('debug', $scriptProperties);
$eventsRaw = $modx->getOption('events', $scriptProperties, array());
if (empty($eventsRaw)) {
	$debugOutput .= "No data to prepare for render.\n";
}
$elStartDate = $modx->getOption('elStartDate', $scriptProperties);
$elEndDate = $modx->getOption('elEndDate', $scriptProperties);
$displayType = $modx->getOption('displayType', $scriptProperties);
$elDirectional = $modx->getOption('elDirectional',$scriptProperties, false);
$limit = $modx->getOption('limit',$scriptProperties,'99');

if($debug) $debugOutput .= 'Looping through events list of '.count($arrEventDates).' total.<br />';
$ulimit=0;

$arrayEventTimer = new makeProcessTime(null,$debug);

$cnt = 1;
$events = [];
foreach ($eventsRaw AS $k=>$e) {
	$oDetails = $arrEventsDetail[$e['eventId']]; //Get original event (parent) details
	$oDetails['startdate'] = $e['date'];
	$oDetails['enddate'] = strtotime('+'.($arrEventsDetail[$e['eventId']]['durDay'] ? $arrEventsDetail[$e['eventId']]['durDay'].' days ' :'').($arrEventsDetail[$e['eventId']]['durHour'] ? $arrEventsDetail[$e['eventId']]['durHour'].' hour ' :'').($arrEventsDetail[$e['eventId']]['durMin'] ? $arrEventsDetail[$e['eventId']]['durMin'].' minute' :''), $e['date']);//$e['date'];//repeatenddate
	if((
		(
			($oDetails['startdate']>=$elStartDate || $oDetails['enddate'] >= $elStartDate)
			&&
			$oDetails['enddate']<=$elEndDate
		)
		||
		$displayType=='detail' || $displayType=='calendar' || $displayType == 'mini'
		||
		$elDirectional
	)){

		$oDetails['startdate_fstamp'] = $e['date'];
		$oDetails['enddate_fstamp'] = $arrEventsDetail[$e['eventId']]['enddate'];

		$oDetails['detailURL'] = $modx->makeUrl((!empty($ajaxResourceId) && (bool)$modalView === true ? $ajaxResourceId : $resourceId),'',array('detail' => $e['eventId'], 'r'=>$e['repeatId']));
		$events[strftime('%Y-%m-%d', $e['date'])][] = $oDetails;
		$ulimit++;
		if($debug) $debugOutput .= $cnt.')&nbsp;&nbsp;&nbsp;&nbsp;'.$ulimit.'['.$limit.']) '.strftime($dateFormat,$e['date']).' '.$e['eventId'].'<br />';
		if($ulimit >= $limit && $displayType=='list' ) break;
		$cnt++;
	}
}
$arrayEventTimer->end('arrEventDates');

// Indexed array - in order to be used with list().
return array($events,$debugOutput);