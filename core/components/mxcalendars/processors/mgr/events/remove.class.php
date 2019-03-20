<?php

/**
 * Class EventsRemoveProcessor
 *
 * Remove Event item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventsRemoveProcessor extends modObjectRemoveProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'mxCalendarEvents';

    /**
     * @access public.
     * @var array
     */
    public $languageTopics = ['mxcalendars:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'mxcalendars.event';
}
return 'EventsRemoveProcessor';

