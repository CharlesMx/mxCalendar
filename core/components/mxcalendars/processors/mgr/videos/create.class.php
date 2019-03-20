<?php

/**
 * Class EventVideosCreateProcessor
 *
 * Create Event Video item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventVideosCreateProcessor extends modObjectCreateProcessor
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


    public function beforeSet()
    {
        $title = $this->getProperty('title');
        if (empty($title)) {
            $this->addFieldError('title', $this->modx->lexicon('mxcalendars.err_event_video_req_name'));
        }

        $video = $this->getProperty('video');
        if (empty($video)) {
            $this->addFieldError('video', $this->modx->lexicon('mxcalendars.err_event_video_req_filepath'));
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
return 'EventVideosCreateProcessor';
