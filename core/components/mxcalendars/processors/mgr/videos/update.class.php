<?php

/**
 * Class EventVideosUpdateProcessor
 *
 * Update Event Video item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventVideosUpdateProcessor extends modObjectUpdateProcessor
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

    /**
     * @return bool
     */
    public function beforeSet(): bool
    {
        $id = $this->getProperty('id');
        if (empty($id)) {
            $this->addFieldError('id', 'ID Not found ' . $this->modx->lexicon('mxcalendars.mxcalendars_err_ns'));
        }
        //-- Now check to make sure that the calendar item exist and can be updated
        $mxcalendar = $this->modx->getObject('mxCalendarEventVideos', $id);
        if (empty($mxcalendar)) {
            $this->addFieldError('id', 'ID Not found ' . $this->modx->lexicon('mxcalendars.mxcalendars_err_nf'));
        }

        //-- Validation for the Name field
        $name = $this->getProperty('title');
        if (!isset($name)) {
            $this->addFieldError('title', $this->modx->lexicon('mxcalendars.err_event_video_req_name'));
        }

        $title = $this->getProperty('video');
        if (empty($title)) {
            $this->addFieldError('video', $this->modx->lexicon('mxcalendars.err_event_video_req_video'));
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
return 'EventVideosUpdateProcessor';
