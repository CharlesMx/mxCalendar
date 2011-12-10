<?php
//-- Validation for the Name field
if (empty($scriptProperties['name'])) {
    $modx->error->addField('name',$modx->lexicon('mxcalendars.err_ns_name'));
} else {
    //-- Enforce a duplicate name check
    /*
    $alreadyExists = $modx->getObject('mxCalendarEvents',array('name' => $scriptProperties['name']));
    if ($alreadyExists) {
        $modx->error->addField('name',$modx->lexicon('mxcalendars.err_ae'));
    }
    */
}


if(empty($scriptProperties['startdate_date'])){
    $modx->error->addField('startdate_date', $modx->lexicon('mxcalendars.err_event_req_startdate'));
    return $modx->error->failure(json_encode($_POST));
}
if(empty($scriptProperties['startdate_time']))
    $modx->error->addField('startdate_time', $modx->lexicon('mxcalendars.err_event_req_starttime'));

//return $modx->error->failure('Testing error');

//-- Set the createdby property of the current manager user
if(empty($scriptProperties['createdby'])){
    $scriptProperties['createdby'] = $modx->getLoginUserID();
}

//-- Set the create date with current timestamp
$scriptProperties['createdon'] = time();

//-- Check for any errors
if ($modx->error->hasError()) {
    return $modx->error->failure('There are errors');
    //return $modx->error->failure();
}

 
//-- Get the mxCalendar object and set the values from form
$mxcalendar = $modx->newObject('mxCalendarEvents');
$mxcalendar->fromArray($scriptProperties);

//-- Try to save the new record 
if ($mxcalendar->save() == false) {
    return $modx->error->failure($modx->lexicon('mxcalendars.mxcalendars_err_save'));
}

//-- If no errors return success 
return $modx->error->success('',$mxcalendar);

?>
