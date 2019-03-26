<?php

/**
 * Class CategoriesUpdateProcessor
 *
 * Update Category item
 *
 * @package mxCalendars
 * @subpackage processors
 */
class CategoriesUpdateProcessor extends modObjectUpdateProcessor
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

    /**
     * @return bool
     */
    public function beforeSet(): bool
    {

        //-- Check for the required calendar ID
        $id = $this->getProperty('id');
        if (empty($id)) {
            $this->addFieldError('id', 'ID Not found ' . $this->modx->lexicon('mxcalendars.err_nf'));
        }

        //-- Now check to make sure that the calendar item exist and can be updated
        $mxcalendar = $this->modx->getObject('mxCalendarCategories', $id);
        if (empty($mxcalendar)) {
            $this->addFieldError('id', 'ID Not found ' . $this->modx->lexicon('mxcalendars.mxcalendars_err_nf'));
        }

        //-- Validation for the Name field
        $name = $this->getProperty('name');
        if (!isset($name)) {
            $this->addFieldError('name', $this->modx->lexicon('mxcalendars.err_ns_name'));
        } else {
            //-- Enforce a duplicate name check
            $query = $this->modx->newQuery('mxCalendarCategories');
            $query->where(array(
                'name:LIKE' => '%' . $name . '%',
                'id:!=' => $id,
            ));
            $alreadyExists = $this->modx->getCollection('mxCalendarCategories', $query);
            if ($alreadyExists) {
                $this->addFieldError('name', $this->modx->lexicon('mxcalendars.err_category_ac'));
            }
        }

        $active = $this->getProperty('active');
        if (isset($active) && ((int)$active === 1 || $active === 'on')) {
            $this->setProperty('active', 1);
        } else {
            $this->setProperty('active', 0);
        }

        $disable = $this->getProperty('disable');
        if (isset($disable) && ((int)$disable === 1 || $disable === 'on')) {
            $this->setProperty('disable', 1);
        } else {
            $this->setProperty('disable', 0);
        }

        $isdefault = $this->getProperty('isdefault');
        if (isset($isdefault) && ((int)$isdefault === 1 || $isdefault === 'on')) {
            $this->setProperty('isdefault', 1);
        } else {
            $this->setProperty('isdefault', 0);
        }

        //-- Set the edited by user id based on authenticated user
        $editedBy = $this->getProperty('editedby');
        if (empty($editedBy)) {
            $this->setProperty('editedby', $this->modx->getLoginUserID());
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
return 'CategoriesUpdateProcessor';
