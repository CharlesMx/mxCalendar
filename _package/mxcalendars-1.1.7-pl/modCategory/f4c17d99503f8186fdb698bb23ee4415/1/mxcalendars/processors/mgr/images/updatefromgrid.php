<?php
/* UPDATE THE CALENDAR ITEM */

//-- Check for the required category ID
if (empty($_DATA['id'])) 
	return $modx->error->failure($modx->lexicon('mxcalendars.err_ns'));

if(isset($_DATA['active']))
    $_DATA['active']=1;
else
    $_DATA['active']=0;

//-- Now check to make sure that the category item exist and can be updated
$mxcalendar = $modx->getObject('mxCalendarEventImages',$_DATA['id']);
if (empty($mxcalendar)) 
	return $modx->error->failure($modx->lexicon('mxcalendars.err_nf'));


//-- Set mxcalendar fields
$mxcalendar->fromArray($_DATA);
 
//-- Try to update calendar item
if ($mxcalendar->save() == false) {
    return $modx->error->failure($modx->lexicon('mxcalendars.err_save'));
}

//-- Return success message if no error was found on update (save)
return $modx->error->success('',$mxcalendar);
?>
