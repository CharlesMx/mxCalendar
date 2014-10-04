<?php

if(isset($scriptProperties['active']))
    $scriptProperties['active']=1;
else
    $scriptProperties['active']=0;

//-- Get the mxCalendar object and set the values from form
$mxcalendar = $modx->newObject('mxCalendarEventImages');
$mxcalendar->fromArray($scriptProperties);

//-- Try to save the new record 
if ($mxcalendar->save() == false) {
    return $modx->error->failure($modx->lexicon('mxcalendars.err_save'));
}

//-- If no errors return success 
return $modx->error->success('',$mxcalendar);

?>
