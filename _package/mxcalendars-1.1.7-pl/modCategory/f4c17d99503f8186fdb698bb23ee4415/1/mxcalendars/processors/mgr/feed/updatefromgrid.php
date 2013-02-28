<?php
/* UPDATE THE CATEGORY ITEM */
/* parse JSON */
if (empty($_REQUEST['data'])) return $modx->error->failure('Invalid data (1a).');
    $_DATA = $modx->fromJSON($_REQUEST['data']);
if (!is_array($_DATA)) return $modx->error->failure('Invalid data (1b).');

//-- Check for the required category ID
if (empty($_DATA['id'])) 
	return $modx->error->failure($modx->lexicon('mxcalendars.err_ns'));

//-- Now check to make sure that the category item exist and can be updated
$mxcalendar = $modx->getObject('mxCalendarFeed',$_DATA['id']);
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
