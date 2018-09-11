<?php

class mxCalendarsIndexManagerController extends modExtraManagerController
{
    public function process(array $scriptProperties = array())
    {
    }

    public function getPageTitle()
    {
        return 'mxCalendar';
    }

    public function getTemplateFile()
    {
        require_once dirname(dirname(__FILE__)) . '/model/mxcalendars/mxcalendars.class.php';
        $mxcalendars = new mxCalendars($modx);
        return $mxcalendars->initialize('mgr');
    }
}
