<?php

/**
 * Class FeedsGetListProcessor
 *
 * Get Feeds list
 *
 * @package mxCalendars
 * @subpackage processors
 */
class FeedsGetListProcessor extends modObjectGetListProcessor
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
    public $objectType = 'mxcalendars.feed';

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
            'mxCalendarFeed.*',
        ]);
        if (!empty($query)) {
            $c->where(array(
                'feed:LIKE' => '%' . $query . '%',
            ));
        }

        $c->sortby($sort, $dir);
        if ($isLimit) {
            $c->limit($limit, $start);
        }

        return $c;
    }
}
return 'FeedsGetListProcessor';