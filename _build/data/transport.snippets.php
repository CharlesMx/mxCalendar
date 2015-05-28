<?php
function getSnippetContent($filename) {
    $o = file_get_contents($filename);
    $o = trim(str_replace(array('<?php','?>'),'',$o));
    return $o;
}
$snippets = array();

//-- Add the main snippet (1) 
$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'mxCalendar',
    'description' => 'Displays events in either calendar, list, or detail view.',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.mxcalendar.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.mxcalendar.php';
$snippets[1]->setProperties($properties);
unset($properties);

//-- Add init snippet (2)
$snippets[2]= $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
	'id' => 2,
	'name' => 'mxCalendarInit',
	'description' => 'Init mxCalendar service.',
	'snippet' => getSnippetContent($sources['elements'].'snippets/mxcalendarinit.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.mxcalendarinit.php';
$snippets[2]->setProperties($properties);
unset($properties);

//-- Add getEvents snippet (3)
$snippets[3]= $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
	'id' => 3,
	'name' => 'mxCalendarGetEvents',
	'description' => 'Get events (retrieved from database together with generated on-the-fly by repeat template).',
	'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.mxcalendargetevents.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.mxcalendargetevents.php';
$snippets[3]->setProperties($properties);
unset($properties);

//-- Add sort snippet (4)
$snippets[4]= $modx->newObject('modSnippet');
$snippets[4]->fromArray(array(
	'id' => 4,
	'name' => 'mxCalendarSort',
	'description' => 'Sort events.',
	'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.mxcalendarsort.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.mxcalendarsort.php';
$snippets[4]->setProperties($properties);
unset($properties);

//-- Add prepare for render snippet (5)
$snippets[5]= $modx->newObject('modSnippet');
$snippets[5]->fromArray(array(
	'id' => 5,
	'name' => 'mxCalendarPrerender',
	'description' => 'Prepare events for render.',
	'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.mxcalendarprerender.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.mxcalendarprerender.php';
$snippets[5]->setProperties($properties);
unset($properties);
 
return $snippets;