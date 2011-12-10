<?php
/* UPDATE THE CALENDAR ITEM */
//-- A little date helper function
if(!@file_exists(dirname(dirname(__FILE__)).'/mxcHelper.php') ) {
    echo 'can not include mxcHelper file.';
} else {
   include(dirname(dirname(__FILE__)).'/mxcHelper.php');
}

//-- Check for the required calendar ID
if (empty($scriptProperties['id'])) 
	return $modx->error->failure($modx->lexicon('mxcalendars.err_ns'));

//-- Now check to make sure that the calendar item exist and can be updated
$mxcalendar = $modx->getObject('mxCalendarEvents',$scriptProperties['id']);
if (empty($mxcalendar)) 
	return $modx->error->failure($modx->lexicon('mxcalendars.err_nf'));

//-- Set the edited by user id based on authenticated user
if(empty($scriptProperties['editedby'])){
    $scriptProperties['editedby'] = $modx->getLoginUserID();
}

//-- Set the edited date/time stamp
$scriptProperties['editedon'] = time();

//-- Combine the multiple date and time fields to single unix time stamp
//$date1=mktime(0, 0, 0, date("m", $date1), date("d", $date1)+1, date("Y", $date1)); 
//mktime(0, 0, 0, 12, 32, 1997);



//-- Set mxcalendar fields
$mxcalendar->fromArray($scriptProperties);
 
//-- Try to update calendar item
if ($mxcalendar->save() == false) {
    return $modx->error->failure($modx->lexicon('mxcalendars.err_save'));
}

//-- Return success message if no error was found on update (save)
return $modx->error->success('',$mxcalendar);
?>
