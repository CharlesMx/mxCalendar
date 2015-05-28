<?php
/**
 * mxCalendar sorting.
 *
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @var $events array array of events to sort
 * @var $direction 'ASC'|'DESC' direction of sorting
 * @var $debug bool
 *
 * @return array(
 *   'events',      // sorted array of events
 *   'debugOutput', // string, containing information, valuable for debugging
 */
$mxCal = $modx->runSnippet('mxCalendar.init', $scriptProperties);

$debugOutput = '';
$debug = $modx->getOption('debug', $scriptProperties);
$events = $modx->getOption('events', $scriptProperties, array());
// @todo Review Default value is not applied - have to intialize as array explicitly.
$events = !empty($events) ? $events : array();
if (empty($events)) {
	$debugOutput .= "No data to sort.\n";
}
$direction = $modx->getOption('direction', $scriptProperties, 'ASC');

// Obtain a list of columns
$timer_5 = new makeProcessTime(null,$debug);
$date = array();
$event = array();
foreach ($events as $key => $row) {
	$date[$key]  = $row['date'];
	$event[$key] = $row['eventId'];
}
$timer_5->end('generate list of all columns for sorting');
// Sort the data with volume descending, edition ascending
// Add $data as the last parameter, to sort by the common key
$multiSortTimer = new makeProcessTime(null,$debug);
$dir = ($direction == 'DESC') ? SORT_DESC : SORT_ASC;
array_multisort($date, $dir, $event, $dir, $events);
$multiSortTimer->end('array_multisort');

// Indexed array - in order to be used with list().
return array($events,$debugOutput);