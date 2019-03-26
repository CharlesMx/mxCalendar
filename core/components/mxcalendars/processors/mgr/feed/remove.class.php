<?php

/**
 * Class FeedsRemoveProcessor
 *
 * Remove Feed item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class FeedsRemoveProcessor extends modObjectRemoveProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'mxCalendarFeed';

    /**
     * @access public.
     * @var array
     */
    public $languageTopics = ['mxcalendars:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'mxcalendars.feed';
}
return 'FeedsRemoveProcessor';

