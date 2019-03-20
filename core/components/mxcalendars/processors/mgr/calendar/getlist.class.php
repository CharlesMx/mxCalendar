<?php

/**
 * Class CalendarsGetListProcessor
 *
 * Get Calendars list
 *
 * @package mxCalendars
 * @subpackage processors
 */
class CalendarsGetListProcessor extends modObjectGetListProcessor
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
    public $defaultSortField = 'id';

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'ASC';

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'mxcalendars.calendar';

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $isLimit = !empty($this->getProperty('limit'));
        $start = $this->getProperty('start');
        $limit = $this->getProperty('limit');
        $sort = $this->getProperty('sort');
        $dir = $this->getProperty('dir');
        $query = $this->getProperty('query');

        /* build query */
        $c->select([
            'mxCalendarCalendars.*',
        ]);
        if (!empty($query)) {
            $c->where(array(
                'name:LIKE' => '%' . $query . '%',
            ));
        }

        $c->sortby($sort, $dir);
        if ($isLimit) {
            $c->limit($limit, $start);
        }

        return $c;
    }
}
return 'CalendarsGetListProcessor';