<?php

/**
 * Class FeedsCreateProcessor
 *
 * Create Feed item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class FeedsCreateProcessor extends modObjectCreateProcessor
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


    public function beforeSet()
    {
        $title = $this->getProperty('feed');
        if (empty($title)) {
            $this->addFieldError('feed', $this->modx->lexicon('mxcalendars.err_feed_req_feed'));
        }

        $defaultcategoryid = $this->getProperty('defaultcategoryid');
        if (empty($defaultcategoryid)) {
            $this->addFieldError('defaultcategoryid', $this->modx->lexicon('mxcalendars.err_feed_req_default_category'));
        }

        $type = $this->getProperty('type');
        if (empty($type)) {
            $this->addFieldError('type', $this->modx->lexicon('mxcalendars.err_feed_req_type'));
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
return 'FeedsCreateProcessor';
