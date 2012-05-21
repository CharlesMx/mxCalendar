<?php
/**
 * Build the setup options form.
 *
 * @package mxCalendar
 * @subpackage build
 */
/* set some default values */
$values = array(
    'defaultCategoryName' => 'Default'
    ,'addDefaultCat' => true
    ,'category_required' => 'true'
    ,'event_desc_type' => 'htmleditor'
);

$output = '';
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $output .= '<h2>mxCalendar Installer</h2>
        <p>You will now begin the install of mxCalendar, please review any/all options carefully.<br /><br />Thank You.</p><br />';
        
        $output .= '<label for="addDefaultCat">Add Default Category: <small>(Note: If not selected you need to create a category before entering any events)</small></label>
        <input type="checkbox" name="addDefaultCat" checked="checked" onChange="this.checked ? document.getElementById(\'catname\').style.visibility=\'hidden\' : document.getElementById(\'catname\').style.visibility=\'\' "/><br /><br />';
        
        $output .= '<div id="catname"><label for="mxc-catname">Default Category Name:</label>
        <input type="text" name="defaultCategoryName" id="defaultCategoryName" width="450" value="'.$values['defaultCategoryName'].'" /></div>
        <br /><br />';
        
        $output .= '<div><label for="mxc-catreq">Require Events to have a Category:</label>
                        <select name="mxc-catreq"><option value="true">Yes</option><option value="false" disable>No</option></select></div><br /><br />';
        
        $output .= '<div><label for="mxc-desctype">Event Description Form Type:</label>
                        <select name="mxc-desctype"><option value="htmleditor">HTML Editor</option><option value="textarea">Text Area (plain text)</option></select></div><br /><br />';
        
        $setting = $modx->getObject('modSystemSetting',array('key' => 'mxcalendars.category_required'));
        if ($setting != null) { $values['category_required'] = $setting->get('value'); }
        unset($setting);
 
        $setting = $modx->getObject('modSystemSetting',array('key' => 'mxcalendars.event_desc_type'));
        if ($setting != null) { $values['event_desc_type'] = $setting->get('value'); }
        unset($setting);

        break;
    case xPDOTransport::ACTION_UPGRADE:
        $output .= '<h2>mxCalendar Update</h2>
        <p>You will now begin the update process for mxCalendar.</p><br />';

        $output .= '<div><label for="mxc-catreq">Require Events to have a Category:</label>
                        <select name="mxc-catreq"><option value="true">Yes</option><option value="false" disable>No</option></select></div><br /><br />';
        
        $output .= '<div><label for="mxc-desctype">Event Description Form Type:</label>
                        <select name="mxc-desctype"><option value="htmleditor">HTML Editor</option><option value="textarea">Text Area (plain text)</option></select></div><br /><br />';
        
        $setting = $modx->getObject('modSystemSetting',array('key' => 'mxcalendars.category_required'));
        if ($setting != null) { $values['category_required'] = $setting->get('value'); }
        unset($setting);
 
        $setting = $modx->getObject('modSystemSetting',array('key' => 'mxcalendars.event_desc_type'));
        if ($setting != null) { $values['event_desc_type'] = $setting->get('value'); }
        unset($setting);
        
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}


return $output;