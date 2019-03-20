<?php

/**
 * Class EventVideosGetListProcessor
 *
 * Get Event Videos list
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventVideosGetListProcessor extends modObjectGetListProcessor
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
    public $objectType = 'mxcalendars.event_videos';

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
            'mxCalendarEventVideos.*',
        ]);
        if (!empty($query)) {
            $c->where(array(
                'title:LIKE' => '%' . $query . '%',
                'OR:description:LIKE' => '%' . $query . '%',
            ));
        }

        $c->sortby($sort, $dir);
        if ($isLimit) {
            $c->limit($limit, $start);
        }

        return $c;
    }
}
return 'EventVideosGetListProcessor';