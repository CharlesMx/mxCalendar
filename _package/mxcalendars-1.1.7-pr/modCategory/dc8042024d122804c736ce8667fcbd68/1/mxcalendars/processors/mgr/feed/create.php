<?php
//-- Validation for the Name field
if (empty($scriptProperties['feed'])) {
    $modx->error->addField('feed',$modx->lexicon('mxcalendars.err_ns_feed'));
} else {
    //-- Enforce a duplicate name check
    $alreadyExists = $modx->getObject('mxCalendarFeed',array('feed' => $scriptProperties['feed']));
    if ($alreadyExists) {
        $modx->error->addField('feed',$modx->lexicon('mxcalendars.err_ae'));
    }
}

//-- Check for any errors
if ($modx->error->hasError()) { return $modx->error->failure(); }

if(isset($scriptProperties['active']))
    $scriptProperties['active']=1;

//-- Get the mxCalendar object and set the values from form
$mxcalendar = $modx->newObject('mxCalendarFeed');
$mxcalendar->fromArray($scriptProperties);

//-- Try to save the new record 
if ($mxcalendar->save() == false) {
    return $modx->error->failure($modx->lexicon('mxcalendars.err_save'));
}

//-- If no errors return success 
return $modx->error->success('',$mxcalendar);

?>
