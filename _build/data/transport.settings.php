<?php

/*********************/
/* Generate settings */
/*********************/

$s = array(
    'core' => array(
        'category_required' => true,
        'event_desc_type' => 'htmleditor',
        'mgr_log_enable' => false,
    ),
    'language' => array(
        'mgr_dateformat' => 'm/d/Y',
        'mgr_timeformat' => 'g:i a',
        'mgr_time_increment' => 15,
        'mgr_allday_start' => '8:00 am',
        'mgr_allday_end' => '5:00 pm',
    ),
    'editor' => array(
        'use_richtext' => true,
        'tiny.width' => '95%',
        'tiny.height' => 200,
        'tiny.buttons1' => 'undo,redo,selectall,pastetext,pasteword,charmap,separator,image,modxlink,unlink,media,separator,code,help',
        'tiny.buttons2' => 'bold,italic,underline,strikethrough,sub,sup,separator,bullist,numlist,outdent,indent,separator,justifyleft,justifycenter,justifyright,justifyfull',
        'tiny.buttons3' => 'styleselect,formatselect,separator,styleprops',
        'tiny.buttons4' => '',
        'tiny.buttons5' => '',
        'tiny.custom_plugins' => '',
        'tiny.theme' => '',
        'tiny.theme_advanced_blockformats' => '',
        'tiny.theme_advanced_css_selectors' => '',
    )
);

$namespace = 'mxcalendars';
$settings = array();

foreach ($s as $area => $sets) {
    foreach ($sets as $key => $value) {
        if (is_bool($value)) {
            $type = 'combo-boolean';
        } else {
            $type = 'textfield';
        }
        $settings[$namespace . '.' . $key] = $modx->newObject('modSystemSetting');
        $settings[$namespace . '.' . $key]->fromArray(array(
            'key' => $namespace . '.' . $key,
            'name' => $namespace . '.setting_' . $key,
            'description' => $namespace . '.setting_' . $key . '_desc',
            'value' => $value,
            'xtype' => $type,
            'namespace' => $namespace,
            'area' => $area
        ), '', true, true);
    }
}

return $settings;
