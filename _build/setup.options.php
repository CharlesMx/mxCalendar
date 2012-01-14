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
);

$output = '';
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $output .= '<h2>mxCalendar Installer</h2>
        <p>You will now begin the install of mxCalendar, please review any/all options carefully.<br /><br />Thank You.</p><br />';
        
        $output .= '<label for="addDefaultCat">Add Default Category:</label>
        <input type="checkbox" name="addDefaultCat" checked="checked" onChange="this.checked ? document.getElementById(\'catname\').style.visibility=\'hidden\' : document.getElementById(\'catname\').style.visibility=\'\' "/><br /><br />';
        
        $output .= '<div id="catname"><label for="mxc-catname">Default Category Name:</label>
        <input type="text" name="defaultCategoryName" id="defaultCategoryName" width="450" value="'.$values['defaultCategoryName'].'" /></div>
        <br /><br />';

        break;
    case xPDOTransport::ACTION_UPGRADE:
        $output .= '<h2>mxCalendar Update</h2>
        <p>You will now begin the update of mxCalendar.</p><br />';
        
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}


return $output;