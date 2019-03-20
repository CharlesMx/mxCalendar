<?php
/* UPDATE THE CALENDAR ITEM */
//-- A little date helper function
if (!@file_exists(dirname(dirname(__FILE__)) . '/mxcHelper.php')) {
    echo 'can not include mxcHelper file.';
} else {
    include(dirname(dirname(__FILE__)) . '/mxcHelper.php');
}

/**
 * Class EventsUpdateProcessor
 *
 * Update Event item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventsUpdateProcessor extends modObjectUpdateProcessor
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

    /**
     * @return bool
     */
    public function beforeSet(): bool
    {
        $title = $this->getProperty('title');
        if (empty($title)) {
            $this->addFieldError('title', $this->modx->lexicon('mxcalendars.err_event_req_name'));
        }

        $id = $this->getProperty('id');
        if (empty($id)) {
            $this->addFieldError('id', 'ID Not found ' . $this->modx->lexicon('mxcalendars.err_nf'));
        }

        //-- Now check to make sure that the calendar item exist and can be updated
        $mxcalendar = $this->modx->getObject('mxCalendarEvents', $id);
        if (empty($mxcalendar)) {
            $this->addFieldError('id', 'ID Not found ' . $this->modx->lexicon('mxcalendars.err_nf'));
        }
        //-- Server Side Validation of Required Fields
        if (!$this->modx->user->isMember('Administrator') && empty($this->getProperty('context'))) {
            $this->addFieldError('context', $this->modx->lexicon('mxcalendars.err_event_req_context'));
        }

        if (empty($this->getProperty('startdate_date'))) {
            $this->addFieldError('startdate_date', $this->modx->lexicon('mxcalendars.err_event_req_startdate'));
        }
        if (empty($this->getProperty('startdate_time'))) {
            $this->addFieldError('startdate_time', $this->modx->lexicon('mxcalendars.err_event_req_starttime'));
        }
        if (empty($this->getProperty('enddate_date'))) {
            $this->addFieldError('enddate_date', $this->modx->lexicon('mxcalendars.err_event_req_enddate'));
        }
        if (empty($this->getProperty('enddate_time'))) {
            $this->addFieldError('enddate_time', $this->modx->lexicon('mxcalendars.err_event_req_endtime'));
        }

        //-- Both date and time are always posted back
        if ($this->getProperty('updatefromgrid') !== 1) {
            if (!empty($this->getProperty('startdate_date')) && !empty($this->getProperty('startdate_time'))) {
                $this->setProperty('startdate',
                    tstamptotime($this->getProperty('startdate_date'), $this->getProperty('startdate_time'),
                        true));
            }
            if (!empty($this->getProperty('enddate_date')) && !empty($this->getProperty('enddate_time'))) {
                $this->setProperty('enddate',
                    tstamptotime($this->getProperty('enddate_date'), $this->getProperty('enddate_time'),
                        true));
            }
            if (!empty($this->getProperty('repeatenddate'))) {
                $this->setProperty('repeatenddate', tstamptotime($this->getProperty('repeatenddate')));
            } else {
                $this->setProperty('repeatenddate', null);
            }
        } else {
            $this->setProperty('repeatenddate', strtotime($this->getProperty('repeatenddate')));
            if (!empty($this->getProperty('startdate_date')) && !empty($this->getProperty('startdate_time'))) {
                $format = 'm.d. Y';
                $dateobj = DateTime::createFromFormat($format, $this->getProperty('startdate_date'));
                $startDate = strtotime($dateobj->format('Y-m-d') . 'T' . date('H:i:s', strtotime($this->getProperty('startdate_time'))));
                $this->setProperty('startdate', $startDate);
            }
            if (!empty($this->getProperty('enddate_date')) && !empty($this->getProperty('enddate_time'))) {
                $format = 'm.d. Y';
                $dateobj = DateTime::createFromFormat($format, $this->getProperty('enddate_date'));
                $endDate = strtotime($dateobj->format('Y-m-d') . 'T' . date('H:i:s', strtotime($this->getProperty('enddate_time'))));
                $this->setProperty('enddate', $endDate);
            }
        }

        if ((int)$this->getProperty('repeating') === 1) {
            //-- Do some error checking just for repeating dates
            if (null === $this->getProperty('repeattype')) {
                $this->addFieldError('repeattype', $this->modx->lexicon('mxcalendars.err_event_req_repeattype'));
            } elseif (empty($this->getProperty('repeaton')) && (int)$this->getProperty('repeattype') === 1) {
                $this->addFieldError('repeaton', $this->modx->lexicon('mxcalendars.err_event_req_repeaton'));
            } elseif (empty($this->getProperty('repeatfrequency')) && ((int)$this->getProperty('repeattype') === 2 || (int)$this->getProperty('repeattype') === 3)) {
                $this->addFieldError('repeatfrequency', $this->modx->lexicon('mxcalendars.err_event_req_repeatfrequency'));
            }

            if (empty($this->getProperty('repeatenddate'))) {
                $this->addFieldError('repeatenddate', $this->modx->lexicon('mxcalendars.err_event_req_repeatenddate'));
            }
        }

        //-- Check if we have all the data to create the repeating field information
        if ((int)$this->getProperty('repeating') === 1 && null !== $this->getProperty('repeattype') && null !== $this->getProperty('repeatfrequency') && !empty($this->getProperty('repeatenddate'))) {
            $repeatDates = _getRepeatDates(
                $this->getProperty('repeattype'),
                $this->getProperty('repeatfrequency'),
                365,
                $this->getProperty('startdate'),
                $this->getProperty('repeatenddate'),
                explode(',', substr($this->getProperty('repeaton'), 1))
            );
            $scriptProperties['repeatdates'] = $repeatDates;
            $scriptProperties['repeatenddate'] = end(explode(',', $repeatDates));
        }

        //-- Category check for required by submission and settings
        if (empty($this->getProperty('categoryid'))) {
            $default_cat = $this->modx->getObject('mxCalendarCategories', [
                'isdefault' => 1
            ]);
            if ($default_cat) {
                if ($default_cat->get('id')) {
                    $scriptProperties['categoryid'] = $default_cat->get('id');
                } else {
                    //-- Get the first published category
                    $default_cat = $this->modx->getObject('mxCalendarCategories', [
                        'active' => 1
                    ]);
                    if ($default_cat->get('id')) {
                        $scriptProperties['categoryid'] = $default_cat->get('id');
                    } else {
                        return $this->modx->error->failure($this->modx->lexicon('mxcalendars.err_event_req_validcat'));
                    }
                }
            }
        }

        if ($this->getProperty('source') === 'feed') {
            $scriptProperties['source'] = 'feed-manual-change';
        }

        //-- Set the edited by user id based on authenticated user
        $editedBy = $this->getProperty('editedby');
        if (empty($editedBy)) {
            $this->setProperty('editedby', $this->modx->getLoginUserID());
        }

        $active = $this->getProperty('active');
        if (isset($active) && ((int)$active === 1 || $active === 'on')) {
            $this->setProperty('active', 1);
        } else {
            $this->setProperty('active', 0);
        }

        //-- Set the edited date/time stamp
        $this->setProperty('editedon', time());

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
return 'EventsUpdateProcessor';
