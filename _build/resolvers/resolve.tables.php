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
            
            //-- ADD ANY ADDITIONAL PROPERTIES TO SET
            if(isset($options['addDefaultCat']) && !empty($options['defaultCategoryName'])){
                $setting = $modx->newObject('mxCalendarCategories');
                $setting->set('name',$options['defaultCategoryName']);
                $setting->set('isdefault', 1);

                if( $setting->save() ){
                    $modx->log(xPDO::LOG_LEVEL_INFO, '[mxCalendar] default category <strong>:'.$options['defaultCategoryName'].'</strong> was created.');
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,'[mxCalendar] default category could not be created');                
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
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('mxcalendars.core_path',null,$modx->getOption('core_path').'components/mxcalendars/').'model/';
            $modx->addPackage('mxcalendars',$modelPath);
 
            $m = $modx->getManager();
            $m->createObjectContainer('mxCalendarCalendars');
            $m->addField('mxCalendarEvents','context');
            $m->addField('mxCalendarEvents','calendar_id');
            $m->addField('mxCalendarEvents','form_chunk');
           
            
            //-- Require Category
            $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'mxcalendars.category_required'));
            if ($setting != null) {
                $setting->set('value',$options['mxc-catreq']);
                $setting->save();
            } else {
                $object->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[mxCalendar:Upgrade] the category setting was not saved for the requirement setting.');
            }

            //-- Set the event description input field type (htmleditor|textarea)
            $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'mxcalendars.event_desc_type'));
            if ($setting != null) {
                $setting->set('value',$options['mxc-desctype']);
                $setting->save();
            } else {
                $object->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[mxCalendar:Upgrade] was unable to set default input type for event description.');
            }
            $success = true;
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success= true;
            break;

    }
}
return $success;