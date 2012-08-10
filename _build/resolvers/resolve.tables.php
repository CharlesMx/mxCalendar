<?php
/**
 * Resolves setup-options settings by setting email options.
 *
 * @package mxCalendar
 * @subpackage build
 */
$success= false;

if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {

        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'model/';
            $modx->addPackage('mxcalendars',$modelPath);
 
            $m = $modx->getManager();
            
            $created_calendar = $m->createObjectContainer('mxCalendarEvents');
            $created_cats = $m->createObjectContainer('mxCalendarCategories');
            $created_settings = $m->createObjectContainer('mxCalendarSettings');
            $created_eventWUG = $m->createObjectContainer('mxCalendarEventWUG');
            $m->createObjectContainer('mxCalendarCalendars');

            $c = $modx->newQuery('mxCalendarCategories');
            $count = $modx->getCount('mxCalendarCategories',$c);
            if(!$count){
                $mxcalendar_cat = $modx->newObject('mxCalendarCategories');
                $mxcalendar_cat->fromArray(array('name'=>$options['defaultCategoryName'],'isdefault'=>1,'active'=>1));
                $mxcalendar_cat->save();
            }
            
            $c = $modx->newQuery('mxCalendarCalendars');
            $count = $modx->getCount('mxCalendarCalendars',$c);
            if(!$count){
                $mxcalendar = $modx->newObject('mxCalendarCalendars');
                $mxcalendar->fromArray(array('name'=>$options['defaultCategoryName'], 'active'=>1));
                $mxcalendar->save();
            }
            //-- ADD ANY ADDITIONAL PROPERTIES TO SET
            if(isset($options['addDefaultCat']) && !empty($options['defaultCategoryName'])){
                $d = $modx->getCount('mxCalendarCategories',array('isdefault' => 1,));
                if($d <= 0){
                    $setting = $modx->newObject('mxCalendarCategories');
                    $setting->set('name',$options['defaultCategoryName']);
                    $setting->set('isdefault', 1);

                    if( $setting->save() ){
                        $modx->log(xPDO::LOG_LEVEL_INFO, '[mxCalendar] default category <strong>:'.$options['defaultCategoryName'].'</strong> was created.');
                    } else {
                        $modx->log(xPDO::LOG_LEVEL_ERROR,'[mxCalendar] default category could not be created');                
                    }
                } else {
                    $modx->log(xPDO::LOG_LEVEL_INFO, '[mxCalendar] default category <strong> already set.');
                }
            }
            
            //-- Require Category
            $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'mxcalendars.category_required'));
            if ($setting != null) {
                $setting->set('value',$options['mxc-catreq']);
                $setting->save();
            } else {
                $object->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[mxCalendar] the category setting was not saved for the requirement setting.');
            }

            //-- Set the event description input field type (htmleditor|textarea)
            $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'mxcalendars.event_desc_type'));
            if ($setting != null) {
                $setting->set('value',$options['mxc-desctype']);
                $setting->save();
            } else {
                $object->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[mxCalendar] was unable to set default input type for event description.');
            }
            
            $success= true;
            break;
            
        case xPDOTransport::ACTION_UPGRADE:
            $success = true;
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success= true;
            break;

    }
}
return $success;