<?php

/**
 * Class EventVideosRemoveProcessor
 *
 * Remove Event Video item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventVideosRemoveProcessor extends modObjectRemoveProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'mxCalendarEventVideos';

    /**
     * @access public.
     * @var array
     */
    public $languageTopics = ['mxcalendars:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'mxcalendars.event_videos';
}
return 'EventVideosRemoveProcessor';

