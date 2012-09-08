<?php
//-- A little date helper function
if(!@file_exists(dirname(dirname(__FILE__)).'/mxcHelper.php') ) {
    echo 'can not include mxcHelper file.';
} else {
   include(dirname(dirname(__FILE__)).'/mxcHelper.php');
}

/* parse JSON */
if (empty($_REQUEST['data'])) return $modx->error->failure('Invalid data (1a).');
    $_DATA = $modx->fromJSON($_REQUEST['data']);
if (!is_array($_DATA)) return $modx->error->failure('Invalid data.');
 
/* get obj */
if (empty($_DATA['id'])) return $modx->error->failure($modx->lexicon('mxcalendars.mxcalendars_err_ns'));
$mxcalendar = $modx->getObject('mxCalendarEvents',$_DATA['id']);
if (empty($mxcalendar)) return $modx->error->failure($modx->lexicon('mxcalendars.mxcalendars_err_nf'));

if(!$modx->user->isMember('Administrator') && empty($_DATA['context']))
    return $modx->error->failure($modx->lexicon('mxcalendars.err_event_req_context'));


// Check for external source flag
if($_DATA['feeds_id'] || $_DATA['source'] != 'local' || $_DATA['feeds_id']){
    //-- Allow only the actual active flag to be updated
    $mxcalendar->set('active',$_DATA['active']);
} else {
    //-- Both date and time are always posted back
    $_DATA['startdate'] = tstamptotime($_DATA['startdate_date'],$_DATA['startdate_time'],true);
    $_DATA['enddate'] = tstamptotime($_DATA['enddate_date'],$_DATA['enddate_time'],true);
    $_DATA['repeatenddate'] = !empty($_DATA['repeatenddate'])?tstamptotime($_DATA['repeatenddate'],$_DATA['enddate_time'],true):null;

    if($_DATA['repeating']==1){
        //-- Do some error checking just for repeating dates
        if(empty($_DATA['repeattype']))
            $modx->error->addField('repeattype', $modx->lexicon('mxcalendars.err_event_req_repeattype'));
        else
            if(empty($_DATA['repeaton']))
                $modx->error->addField('repeaton', $modx->lexicon('mxcalendars.err_event_req_repeaton'));
        if(empty($_DATA['repeatfrequency']))
            $modx->error->addField('repeatfrequency', $modx->lexicon('mxcalendars.err_event_req_repeatfrequency'));
        if(empty($_DATA['repeatenddate']))
            $modx->error->addField('repeatenddate', $modx->lexicon('mxcalendars.err_event_req_repeatenddate'));
    }

    //-- Check if we have all the data to create the repeating field information
    if($_DATA['repeating']==1 && isset($_DATA['repeattype']) && isset($_DATA['repeatfrequency']) && !empty($_DATA['repeatenddate'])){
        $repeatDates = _getRepeatDates(
            $_DATA['repeattype']
            , $_DATA['repeatfrequency']
            ,365
            , $_DATA['startdate']
            , $_DATA['repeatenddate']
            , explode(',', substr($_DATA['repeaton'],1))
            );
        $_DATA['repeatdates'] = $repeatDates;
        $_DATA['repeatenddate'] = end(explode(',', $repeatDates));
    } else { 
        //-- return $modx->error->failure("Repeat criteria not meet .<br>".$_DATA['repeattype']);
    }

    //-- Category check for required by submission and settings
    if(empty($_DATA['categoryid'])){

        $default_cat = $modx->getObject('mxCalendarCategories', array(
        'isdefault' => 1
        ));

        if($default_cat->get('id')){
            $_DATA['categoryid'] = $default_cat->get('id');
        } else {
            //-- Get the first published category
            $default_cat = $modx->getObject('mxCalendarCategories', array(
            'active' => 1
            ));
            if($default_cat->getOne('id')){
                $_DATA['categoryid'] = $default_cat->get('id');
            } else {
                return $modx->error->failure($modx->lexicon('mxcalendars.err_event_req_validcat'));
            }
        }
    }
    //-- Create the object from the json parsed array
    $mxcalendar->fromArray($_DATA);
}

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
