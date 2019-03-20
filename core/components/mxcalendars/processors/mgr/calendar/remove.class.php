<?php

/**
 * Class CalendarsRemoveProcessor
 *
 * Remove Calendar item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class CalendarsRemoveProcessor extends modObjectRemoveProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'mxCalendarCalendars';

    /**
     * @access public.
     * @var array
     */
    public $languageTopics = ['mxcalendars:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'mxcalendars.calendar';
}
return 'CalendarsRemoveProcessor';