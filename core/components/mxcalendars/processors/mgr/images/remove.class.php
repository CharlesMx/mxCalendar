<?php

/**
 * Class EventImagesRemoveProcessor
 *
 * Remove Event Image item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventImagesRemoveProcessor extends modObjectRemoveProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'mxCalendarEventImages';

    /**
     * @access public.
     * @var array
     */
    public $languageTopics = ['mxcalendars:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'mxcalendars.event_images';
}
return 'EventImagesRemoveProcessor';

