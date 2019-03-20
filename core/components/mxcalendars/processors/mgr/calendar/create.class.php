<?php

/**
 * Class CalendarCreateProcessor
 *
 * Create Calendar item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class CalendarsCreateProcessor extends modObjectCreateProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'mxCalendarCalendars';

    /**
     * @access public.
     * @var array
     */
    public $languageTopics = ['mxcalendars:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'mxcalendars.calendar';

    /**
     * @return bool
     */
    public function beforeSet(): bool
    {
        //-- Validation for the Name field
        $name = $this->getProperty('name');
        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('mxcalendars.err_ns_name'));
        } else {
            //-- Enforce a duplicate name check
            $alreadyExists = $this->modx->getObject('mxCalendarCalendars', array('name' => $name));
            if ($alreadyExists) {
                $this->addFieldError('name', $this->modx->lexicon('mxcalendars.err_ac'));
            }
        }

        //  // not implemented yet
        //  //-- Set the createdby property of the current manager user
        //  $createdby = $this->getProperty('createdby');
        //  if (empty($createdby)) {
        //            $this->setProperty('createdby', $this->modx->getLoginUserID());
        //  }
        //
        //  //-- Set the create date with current timestamp
        //  $this->setProperty('createdon', time());

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
return 'CalendarsCreateProcessor';
