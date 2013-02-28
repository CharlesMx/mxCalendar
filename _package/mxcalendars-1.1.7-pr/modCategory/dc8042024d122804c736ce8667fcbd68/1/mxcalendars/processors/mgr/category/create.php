<?php
//-- Validation for the Name field
if (empty($scriptProperties['name'])) {
    $modx->error->addField('name',$modx->lexicon('mxcalendars.err_ns_name'));
} else {
    //-- Enforce a duplicate name check
    $alreadyExists = $modx->getObject('mxCalendarCategories',array('name' => $scriptProperties['name']));
    if ($alreadyExists) {
        $modx->error->addField('name',$modx->lexicon('mxcalendars.err_ae'));
    }
}

//-- Set the createdby property of the current manager user
if(empty($scriptProperties['createdby'])){
    $scriptProperties['createdby'] = $modx->getLoginUserID();
}

//-- Set the create date with current timestamp
$scriptProperties['createdon'] = time();

//-- Check for any errors
if ($modx->error->hasError()) { return $modx->error->failure(); }

if(isset($scriptProperties['active']))
    $scriptProperties['active']=1;
if(isset($scriptProperties['disabled']))
    $scriptProperties['disabled'] = 1;
if(isset($scriptProperties['isdefault']))
    $scriptProperties['isdefault']=1;

//-- Get the mxCalendar object and set the values from form
$mxcalendar = $modx->newObject('mxCalendarCategories');
$mxcalendar->fromArray($scriptProperties);

//-- Try to save the new record 
if ($mxcalendar->save() == false) {
    return $modx->error->failure($modx->lexicon('mxcalendars.err_save'));
}

//-- If no errors return success 
return $modx->error->success('',$mxcalendar);

?>
