<?php
//-- A little date helper function
if(!@file_exists(dirname(dirname(__FILE__)).'/mxcHelper.php') ) {
    echo 'can not include mxcHelper file.';
} else {
   include(dirname(dirname(__FILE__)).'/mxcHelper.php');
}

//-- Server Side Validation of Required Fields
if (empty($scriptProperties['title']))
    $modx->error->addField('title',$modx->lexicon('mxcalendars.err_ns_title'));
if(empty($scriptProperties['startdate_date']))
    $modx->error->addField('startdate_date', $modx->lexicon('mxcalendars.err_event_req_startdate'));
if(empty($scriptProperties['startdate_time']))
    $modx->error->addField('startdate_time', $modx->lexicon('mxcalendars.err_event_req_starttime'));
if(empty($scriptProperties['enddate_date']))
    $modx->error->addField('enddate_date', $modx->lexicon('mxcalendars.err_event_req_enddate'));
if(empty($scriptProperties['enddate_time']))
    $modx->error->addField('enddate_time', $modx->lexicon('mxcalendars.err_event_req_endtime'));

//-- Both date and time are always posted back
$scriptProperties['startdate'] = tstamptotime($scriptProperties['startdate_date'],$scriptProperties['startdate_time'],true);
$scriptProperties['enddate'] = tstamptotime($scriptProperties['enddate_date'],$scriptProperties['enddate_time'],true);
$scriptProperties['repeatenddate'] = tstamptotime($scriptProperties['repeatenddate'],$scriptProperties['enddate_time'],true);

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
