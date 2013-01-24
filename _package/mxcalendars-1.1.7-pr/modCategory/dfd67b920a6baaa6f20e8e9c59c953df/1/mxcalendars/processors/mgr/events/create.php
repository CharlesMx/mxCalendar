<?php
//-- A little date helper function
if(!@file_exists(dirname(dirname(__FILE__)).'/mxcHelper.php') ) {
    echo 'can not include mxcHelper file.';
} else {
   include(dirname(dirname(__FILE__)).'/mxcHelper.php');
}

//-- Server Side Validation of Required Fields
if(!$modx->user->isMember('Administrator') && empty($scriptProperties['context']))
    $modx->error->addField('context', $modx->lexicon('mxcalendars.err_event_req_context'));
if (empty($scriptProperties['title']))
    $modx->error->addField('title',$modx->lexicon('mxcalendars.err_ns_title'));
//if (empty($scriptProperties['categoryid']))
//    $modx->error->addField('categoryid',$modx->lexicon('mxcalendars.err_event_req_category'));
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
$scriptProperties['repeatenddate'] = !empty($scriptProperties['repeatenddate'])?tstamptotime($scriptProperties['repeatenddate'],$scriptProperties['enddate_time'],true):null;


if($scriptProperties['repeating']==1){
    //-- Do some error checking just for repeating dates
    if(empty($scriptProperties['repeattype']))
        $modx->error->addField('repeattype', $modx->lexicon('mxcalendars.err_event_req_repeattype'));
    else
        if(empty($scriptProperties['repeaton']))
            $modx->error->addField('repeaton', $modx->lexicon('mxcalendars.err_event_req_repeaton'));
    if(empty($scriptProperties['repeatfrequency']))
        $modx->error->addField('repeatfrequency', $modx->lexicon('mxcalendars.err_event_req_repeatfrequency'));
    if(empty($scriptProperties['repeatenddate']))
        $modx->error->addField('repeatenddate', $modx->lexicon('mxcalendars.err_event_req_repeatenddate'));
}

//-- Check if we have all the data to create the repeating field information
if($scriptProperties['repeating']==1 && isset($scriptProperties['repeattype']) && isset($scriptProperties['repeatfrequency']) && !empty($scriptProperties['repeatenddate'])){
    $repeatDates = _getRepeatDates(
         $scriptProperties['repeattype']
         , $scriptProperties['repeatfrequency']
         ,365
         , $scriptProperties['startdate']
         , $scriptProperties['repeatenddate']
         , explode(',', substr($scriptProperties['repeaton'],1))
         );
    $scriptProperties['repeatdates'] = $repeatDates;
    $scriptProperties['repeatenddate'] = end(explode(',', $repeatDates));
} else { 
    //-- return $modx->error->failure("Repeat criteria not meet .<br>".$scriptProperties['repeattype']);
}

//-- Category check for required by submission and settings
if(empty($scriptProperties['categoryid'])){

    $default_cat = $modx->getObject('mxCalendarCategories', array(
       'isdefault' => 1
    ));
    
    if($default_cat->get('id')){
        $scriptProperties['categoryid'] = $default_cat->get('id');
    } else {
        //-- Get the first published category
        $default_cat = $modx->getObject('mxCalendarCategories', array(
           'active' => 1
        ));
        if($default_cat->get('id')){
            $scriptProperties['categoryid'] = $default_cat->get('id');
        } else {
            return $modx->error->failure($modx->lexicon('mxcalendars.err_event_req_validcat'));
        }
    }
}

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
} else {
    // Check for any images that are not assigned and assign them now
    $images = $modx->getCollection('mxCalendarEventImages',array('event_id'=>0));
    if($images){
        foreach($images AS $image){
            $image->set('event_id',$mxcalendar->get('id'));
            $image->save();
        }
    }
    
}

//-- If no errors return success 
return $modx->error->success('',$mxcalendar);

?>
