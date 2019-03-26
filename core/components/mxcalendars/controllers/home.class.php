<?php

/**
 * mxCalendars
 *
 * Copyright 2019 by Sterc <modx@sterc.nl>
 */

require_once dirname(__DIR__) . '/index.class.php';

class mxcalendarsHomeManagerController extends mxcalendarsBaseManagerController
{
    public function process(array $scriptProperties = array()) {

    }
    /**
     * @access public.
     */
    public function loadCustomCssJs()
    {
        $this->addJavascript($this->mxCalendars->config['jsUrl'] . 'mgr/widgets/home.panel.js');

        $this->addJavascript($this->mxCalendars->config['jsUrl'] . 'mgr/widgets/mxcalendars.grid.js');
        $this->addJavascript($this->mxCalendars->config['jsUrl'] . 'mgr/widgets/mxcalendars.categories.grid.js');
        $this->addJavascript($this->mxCalendars->config['jsUrl'] . 'mgr/widgets/mxcalendars.calendars.grid.js');
        $this->addJavascript($this->mxCalendars->config['jsUrl'] . 'mgr/widgets/mxcalendars.images.grid.js');
        $this->addJavascript($this->mxCalendars->config['jsUrl'] . 'mgr/widgets/mxcalendars.videos.grid.js');
        $this->addJavascript($this->mxCalendars->config['jsUrl'] . 'mgr/widgets/mxcalendars.feed.grid.js');

        $this->addLastJavascript($this->mxCalendars->config['jsUrl'] . 'mgr/sections/home.js');
    }

    /**
     * @access public.
     * @return String.
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('mxcalendars');
    }

    /**
     * @access public.
     * @return String.
     */
    public function getTemplateFile()
    {
        return $this->mxCalendars->config['templates_path'] . 'home.tpl';
    }
}