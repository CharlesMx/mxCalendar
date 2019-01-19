<?php
/**
 * Build the setup options form.
 *
 * @package mxCalendar
 * @subpackage build
 *
 * @var String $options
 */

/* set some default values */
$values = array(
    'defaultCategoryName' => 'Default',
    'addDefaultCat' => true,
    'category_required' => 'true',
    'event_desc_type' => 'htmleditor'
);

switch ($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        // TODO: Save values correctly
        $setting = $modx->getObject('modSystemSetting', array('key' => 'mxcalendars.category_required'));
        if ($setting != null) {
            $values['category_required'] = $setting->get('value');
        }
        unset($setting);

        $setting = $modx->getObject('modSystemSetting', array('key' => 'mxcalendars.event_desc_type'));
        if ($setting != null) {
            $values['event_desc_type'] = $setting->get('value');
        }
        unset($setting);

        break;

    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

$output = '';
$divider = '<div style="margin-bottom: 20px;"></div>';

$output .= '<h2>mxCalendar Installer</h2>
                    <p>You will now begin the install of mxCalendar, <br>please review all options carefully.</p>';
$output .= '<hr>';

$output .= '<div><label for="mxc-desctype">Event Description Form Type:</label>
                    <select name="mxc-desctype">
                        <option value="htmleditor">HTML Editor</option>
                        <option value="textarea">Text Area (plain text)</option>
                    </select></div>';
$output .= $divider;

$output .= '<div><label for="mxc-catreq">Require Events to have a Category:</label>
                    <select name="mxc-catreq">
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </select></div>';
$output .= $divider;

$output .= '<script>
                     function mxcToggleCatName(checked) {
                         var catNameWrapper = document.getElementById("catname"); 
                         catNameWrapper.style.display = checked ? "block" : "none";
                     }
                    </script>
                    
                    <input type="checkbox" name="addDefaultCat" id="addDefaultCat" checked="checked" onChange="mxcToggleCatName(this.checked)">
                    <label for="addDefaultCat" style="display: inline; cursor: pointer;">Add Default Category</label>
                    <p>
                        <small>(Note: If not selected you need to create a category before entering any events)</small>
                    </p>';
$output .= $divider;

$output .= '<div id="catname"><label for="mxc-catname">Default Category Name:</label>
                    <input type="text" name="defaultCategoryName" id="defaultCategoryName" width="450" value="' . $values['defaultCategoryName'] . '"></div>';
$output .= $divider;

return $output;
