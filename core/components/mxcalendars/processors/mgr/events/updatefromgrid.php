<?php
//-- A little date helper function
if(!@file_exists(dirname(dirname(__FILE__)).'/mxcHelper.php') ) {
    echo 'can not include mxcHelper file.';
} else {
   include(dirname(dirname(__FILE__)).'/mxcHelper.php');
}

/* parse JSON */
if (empty($scriptProperties['data'])) return $modx->error->failure('Invalid data.');
$_DATA = $modx->fromJSON($scriptProperties['data']);
if (!is_array($_DATA)) return $modx->error->failure('Invalid data.');
 
/* get obj */
if (empty($_DATA['id'])) return $modx->error->failure($modx->lexicon('mxcalendars.mxcalendars_err_ns'));
$mxcalendar = $modx->getObject('mxCalendarEvents',$_DATA['id']);
if (empty($mxcalendar)) return $modx->error->failure($modx->lexicon('mxcalendars.mxcalendars_err_nf'));
 
//-- Both date and time are always posted back
$_DATA['startdate'] = tstamptotime($_DATA['startdate_date'],$_DATA['startdate_time'],true);
$_DATA['enddate'] = tstamptotime($_DATA['enddate_date'],$_DATA['enddate_time'],true);

//-- Create the object from the json parsed array
$mxcalendar->fromArray($_DATA);

/** 
 *  Set the new value of the full unix time stamp from both date and time fields
 *  we could have also set this through the $_DATA properties as well before the fromArray line
 */
//$mxcalendar->set('startdate',strtotime($datestamp) );

/* save */
if ($mxcalendar->save() == false) {
    return $modx->error->failure($modx->lexicon('mxcalendars.mxcalendars_err_save'));
}
 
return $modx->error->success('',$mxcalendar);

?>
