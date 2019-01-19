<?php

function getSnippetContent($filename)
{
    $o = file_get_contents($filename);
    $o = trim(str_replace(array('<?php', '?>'), '', $o));
    return $o;
}

$snippets = array();

//-- Add the main snippet (1) 
$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'mxcalendar',
    'description' => 'Displays events in either calendar, list, or detail view.',
    'snippet' => getSnippetContent($sources['elements'] . 'snippets/snippet.mxcalendars.php'),
), '', true, true);
$properties = include $sources['data'] . 'properties/properties.mxcalendars.php';
$snippets[1]->setProperties($properties);
unset($properties);


return $snippets;
