<?php

/**
 * Class CategoriesRemoveProcessor
 *
 * Remove Category item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class CategoriesRemoveProcessor extends modObjectRemoveProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'mxCalendarCategories';

    /**
     * @access public.
     * @var array
     */
    public $languageTopics = ['mxcalendars:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'mxcalendars.category';
}
return 'CategoriesRemoveProcessor';