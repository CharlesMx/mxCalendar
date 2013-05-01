<?php
// SAMPLE EVENT SUBMISSION HOOK FOR mxFormBuilder


$mxcal = $modx->getService('mxcalendars','mxCalendars',$modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'model/mxcalendars/',$scriptProperties);
if (!($mxcal instanceof mxCalendars)) return 'Error loading instance of mxCalendars.';

include_once($modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'processors/mgr/mxcHelper.php');


$allFormFields = $hook->getProperties();

//-- Get the mxCalendar object and set the values from form
$mxcalendar = $modx->newObject('mxCalendarEvents');

$mxcalendar->set('title', $hook->getProperty('fld1'));
$mxcalendar->set('startdate', strtotime($hook->getProperty('fld2')));
$mxcalendar->set('enddate', strtotime($hook->getProperty('fld3')));
$mxcalendar->set('location_name', $hook->getProperty('fld4'));
$mxcalendar->set('description', $hook->getProperty('fld12'));
$mxcalendar->set('location_address', $hook->getProperty('fld5').', '.$hook->getProperty('fld6').', '.$hook->getProperty('fld7'));
$mxcalendar->set('link', $hook->getProperty('fld13'));

// Set the new event to inactive so an Administrator can review and approve prior to listing
$mxcalendar->set('active',0);

//-- Set a fixed (default) category for submissions [Main Calendar]
if($hook->getProperty('fld18')){
  $mxcalendar->set('categoryid', $hook->getProperty('fld18')  );
}

//-- Set a static value of source to indicate it was user generated
$mxcalendar->set('source', 'user submission');

//-- Just some additional record house keeping for repeating
if($hook->getProperty('fld15') !== null && $hook->getProperty('fld16') !== null){
	$frequencymode = $hook->getProperty('fld15'); // Daily, weekly, monthly, yearly
	$onDayOfWeek = array();
	$onDayOfWeek[] = strftime('%u', strtotime($hook->getProperty('fld2')));
	$repeatDates = _getRepeatDates($frequencymode ,1 ,1 , strtotime($hook->getProperty('fld2')) ,strtotime($hook->getProperty('fld16')) ,$onDayOfWeek);

	$mxcalendar->set('repeaton', ','.implode(',',$onDayOfWeek).','); //** this one is required or the backend manager will fail to allow updating do to JS error
	$mxcalendar->set('repeating', 1);
	$mxcalendar->set('repeattype', $frequencymode);
	$mxcalendar->set('repeatdates', (!empty($repeatDates)?$repeatDates:''));
        $mxcalendar->set('repeatenddate',strtotime($hook->getProperty('fld16')));
	$mxcalendar->set('repeatfrequency', 1);
	
	// Add to log report if debug is set
	if($hook->getProperty('debug') == '1') $modx->log(modX::LOG_LEVEL_ERROR,'[mxFormBuilder] <h2>mxFormBuilder</h2> _getRpeatDate:<br />'.$repeatDates.' by passing '.print_r(array($frequencymode ,1 ,1 ,$hook->getProperty('fld2') ,$hook->getProperty('fld16') ,$onDayOfWeek),true) );

} else {
  // No repeat
	$mxcalendar->set('repeaton', ''); //** this one is required or the backend manager will fail to allow updating do to JS error
	$mxcalendar->set('repeating', 0);
	$mxcalendar->set('repeattype', 0);
}

// @TODO - Make field names managable so we can do a quick mapping
//$mxcalendar->fromArray($allFormFields);

// Save the new entry
if($mxcalendar->save()){
	$modx->log(modX::LOG_LEVEL_DEBUG,'[mxFormBuilder::mxCalendar->Submission Save] mxCalendar Submission Saved :: Event Id => '.$mxcalendar->get('id').'<br /><br />');
} else {
	$modx->log(modX::LOG_LEVEL_ERROR,'[mxFormBuilder] <h2>mxFormBuilder</h2> Unable to save mxCalendar submission.'.json_encode($allFormFields).'<br /><br />');
}

$mxformbuilder->hookoutput = '<h2>mxFormBuilder Hooks: Testing</h2>'.json_encode($allFormFields).'<br /><br />';

// We can output some debug info if we set the debug flag in parameters
if($hook->getProperty('debug') == '1'){
	$modx->log(modX::LOG_LEVEL_ERROR,'[mxFormBuilder] <h2>Test mxFormBuilder Hooks</h2>'.json_encode($allFormFields).'<br /><br />');
}

// Tell mxFormBuilder that the hook was successful
return true;
