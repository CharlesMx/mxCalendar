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
        'mgr_allday_start' => '8:00 am',
        'mgr_allday_end' => '5:00 pm',
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

/* Settings for the TinyMCE integration */  
$settings['mxcalendars.use_richtext']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.use_richtext']->fromArray(array(  'key' => 
'mxcalendars.use_richtext',  'value' => false,  'xtype' => 
'combo-boolean',  'namespace' => 'mxcalendar',  'area' => 'TinyMCE',  
),'',true,true);    $settings['mxcalendars.tiny.width']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.width']->fromArray(array(  'key' => 
'mxcalendars.tiny.width',  'value' => '95%',  'xtype' => 'textfield',  
'namespace' => 'mxcalendar',  'area' => 'TinyMCE',  ),'',true,true);    
$settings['mxcalendars.tiny.height']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.height']->fromArray(array(  'key' => 
'mxcalendars.tiny.height',  'value' => 200,  'xtype' => 'textfield',  
'namespace' => 'mxcalendar',  'area' => 'TinyMCE',  ),'',true,true);    
$settings['mxcalendars.tiny.buttons1']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.buttons1']->fromArray(array(  'key' => 
'mxcalendars.tiny.buttons1',  'value' => 
'undo,redo,selectall,pastetext,pasteword,charmap,separator,image,modxl
ink,unlink,media,separator,code,help',  'xtype' => 'textfield',  
'namespace' => 'mxcalendar',  'area' => 'TinyMCE',  ),'',true,true);    
$settings['mxcalendars.tiny.buttons2']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.buttons2']->fromArray(array(  'key' => 
'mxcalendars.tiny.buttons2',  'value' => 
'bold,italic,underline,strikethrough,sub,sup,separator,bullist,numlist
,outdent,indent,separator,justifyleft,justifycenter,justifyright,justi
fyfull',  'xtype' => 'textfield',  'namespace' => 'mxcalendar',  'area' 
=> 'TinyMCE',  ),'',true,true);    
$settings['mxcalendars.tiny.buttons3']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.buttons3']->fromArray(array(  'key' => 
'mxcalendars.tiny.buttons3',  'value' => 
'styleselect,formatselect,separator,styleprops',  'xtype' => 
'textfield',  'namespace' => 'mxcalendar',  'area' => 'TinyMCE',  
),'',true,true);    $settings['mxcalendars.tiny.buttons4']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.buttons4']->fromArray(array(  'key' => 
'mxcalendars.tiny.buttons4',  'value' => '',  'xtype' => 'textfield',  
'namespace' => 'mxcalendar',  'area' => 'TinyMCE',  ),'',true,true);    
$settings['mxcalendars.tiny.buttons5']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.buttons5']->fromArray(array(  'key' => 
'mxcalendars.tiny.buttons5',  'value' => '',  'xtype' => 'textfield',  
'namespace' => 'mxcalendar',  'area' => 'TinyMCE',  ),'',true,true);    
$settings['mxcalendars.tiny.custom_plugins']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.custom_plugins']->fromArray(array(  'key' => 
'mxcalendars.tiny.custom_plugins',  'value' => '',  'xtype' => 
'textfield',  'namespace' => 'mxcalendar',  'area' => 'TinyMCE',  
),'',true,true);    $settings['mxcalendars.tiny.theme']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.theme']->fromArray(array(  'key' => 
'mxcalendars.tiny.theme',  'value' => '',  'xtype' => 'textfield',  
'namespace' => 'mxcalendar',  'area' => 'TinyMCE',  ),'',true,true);    
$settings['mxcalendars.tiny.theme_advanced_blockformats']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.theme_advanced_blockformats']->fromArray(array
(  'key' => 'mxcalendars.tiny.theme_advanced_blockformats',  'value' => 
'',  'xtype' => 'textfield',  'namespace' => 'mxcalendar',  'area' => 
'TinyMCE',  ),'',true,true);    
$settings['mxcalendars.tiny.theme_advanced_css_selectors']= 
$modx->newObject('modSystemSetting');  
$settings['mxcalendars.tiny.theme_advanced_css_selectors']->fromArray(array
( 'key' => 'mxcalendars.tiny.theme_advanced_selectors',  'value' => 
'',  'xtype' => 'textfield',  'namespace' => 'mxcalendar',  'area' => 
'TinyMCE',  ),'',true,true);

return $settings;