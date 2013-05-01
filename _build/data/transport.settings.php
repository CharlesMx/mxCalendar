<?php

/*********************/
/** Add in settings **/
/*********************/
$s = array(
    'mxcalendars' => array(
        'category_required' => true,
        'event_desc_type' => 'htmleditor',
        'mgr_dateformat'=>'m/d/Y',
        'mgr_timeformat'=>'g:i a',
        'mgr_log_enable'=>false,
        'mgr_time_increment'=>15,
    )
);

$settings = array();

$settings = array();
foreach ($s as $area => $sets) {
    foreach ($sets as $key => $value) {
        if (is_bool($value)) { $type = 'combo-boolean'; }
        else { $type = 'textfield'; }
        $settings[$area.'.'.$key] = $modx->newObject('modSystemSetting');
        $settings[$area.'.'.$key]->fromArray(array(
            'key' => $area.'.'.$key,
            'name'=> $area.'.set_'.$key,
            'value' => $value,
            'xtype' => $type,
            'namespace' => $area,
            'area' => $area
        ),'',true,true);
    }
}

return $settings;