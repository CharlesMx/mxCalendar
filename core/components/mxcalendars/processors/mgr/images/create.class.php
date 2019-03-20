<?php

/**
 * Class EventImagesCreateProcessor
 *
 * Create Event Image item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventImagesCreateProcessor extends modObjectCreateProcessor
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


    public function beforeSet()
    {
        $title = $this->getProperty('title');
        if (empty($title)) {
            $this->addFieldError('title', $this->modx->lexicon('mxcalendars.err_event_image_req_name'));
        }

        $filepath = $this->getProperty('filepath');
        if (empty($filepath)) {
            $this->addFieldError('filepath', $this->modx->lexicon('mxcalendars.err_event_image_req_filepath'));
        }

        $active = $this->getProperty('active');
        if (isset($active) && ((int)$active === 1 || $active === 'on')) {
            $this->setProperty('active', 1);
        } else {
            $this->setProperty('active', 0);
        }

        //-- show error messages
        if ($this->hasErrors()) {
            $errors = '';
            foreach($this->modx->error->getFields() as $error) {
                $errors .= $error . '<br />';
            }

            $this->modx->error->failure($errors);
        }

        return parent::beforeSet();
    }
}
return 'EventImagesCreateProcessor';
