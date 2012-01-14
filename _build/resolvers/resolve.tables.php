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
            $success= true;
            break;
            
        case xPDOTransport::ACTION_UPGRADE:
        case xPDOTransport::ACTION_UNINSTALL:
            $success= true;
            break;

    }
}
return $success;