<?php
//-- Make sure a valid category item is passed
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('mxcalendars.mxcalendars_err_ns'));
$mxcalendar = $modx->getObject('mxCalendarCalendars',$scriptProperties['id']);
if (empty($mxcalendar)) return $modx->error->failure($modx->lexicon('mxcalendars.mxcalendars_err_nf'));
 
//-- Remove the calendar record 
if ($mxcalendar->remove() == false) {
    return $modx->error->failure($modx->lexicon('mxcalendars.mxcalendars_err_remove'));
}

//-- If no errors return success 
return $modx->error->success('',$mxcalendar);

?>
