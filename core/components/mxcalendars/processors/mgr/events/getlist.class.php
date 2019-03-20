<?php

/**
 * Class EventsGetListProcessor
 *
 * Get Events list
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventsGetListProcessor extends modObjectGetListProcessor
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
    public $defaultSortField = 'startdate';

    /**
     * @access public.
     * @var String.
     */
    public $defaultSortDirection = 'DESC';

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'mxcalendars.event';

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
        $historical = $this->getProperty('historical');

        //-- Restrict Access Based on User Group Access
        $userWUG_arr = $this->modx->user->getUserGroupNames();
        $userContextACL_arr = array();
        $userid = $this->modx->user->get('id');
        $ug = $this->modx->newQuery('modUserGroup');
        $ug->where(array(
            'name:IN' => $userWUG_arr,
        ));
        $mxc_groups = $this->modx->getIterator('modUserGroup', $ug);
        if (count($mxc_groups))
        {
            foreach ($mxc_groups AS $mxg)
            {

                $webContextAccess = $this->modx->newQuery('modAccessContext');
                $webContextAccess->where(array(
                    'principal' => $mxg->get('id'),
                    'AND:target:!=' => 'mgr',
                ));
                $mxc_cntx = $this->modx->getIterator('modAccessContext', $webContextAccess);

                if (count($mxc_cntx))
                {
                    foreach ($mxc_cntx AS $acl)
                    {
                        if (!in_array($acl->get('target'), $userContextACL_arr))
                            $userContextACL_arr[] = $acl->get('target');
                    }
                }

            }
        }
        if ($this->modx->user->isMember('Administrator')) {
            $userContextACL_arr[] = '';
        }


        /* build query */
        //$c = $this->modx->newQuery('mxCalendarEvents');
        $c->select([
            'mxCalendarEvents.*',
            //'CategoryId.name', 	'CategoryId.foregroundcss', 'CategoryId.backgroundcss', 'CategoryId.inlinecss'
        ]);
        //$c->innerJoin('mxCalendarCategories','CategoryId');
        //$c->innerJoin('categoryid','id','mxCalendarEvents.categoryid = CategoryId');
        if (!empty($query)) {
            $c->where([
                'title:LIKE' => '%' . $query . '%',
                'OR:description:LIKE' => '%' . $query . '%',
                //'OR:CategoryId.name:LIKE'=>'%'.$query.'%',
            ]);
        } else {
            if ($historical) {
                $c->where([
                    'repeating:=' => 0
                    ,
                    'AND:enddate:<=' => time()
                    ,
                    [
                        'OR:repeating:=' => 1
                        ,
                        'AND:repeatenddate:<=' => time()
                    ]
                ]);
            } else {
                $c->where([
                    'repeating:=' => 0
                    ,
                    'AND:enddate:>=' => time()
                    ,
                    [
                        'OR:repeating:=' => 1
                        ,
                        'AND:repeatenddate:>=' => time()
                    ]
                ]);
            }
        }
        if (count($userContextACL_arr)) {
            $c->where(['context:IN' => $userContextACL_arr]);
        }

        //$count = $this->modx->getCount('mxCalendarEvents', $c);
        $c->sortby($sort, $dir);
        if ($isLimit) {
            $c->limit($limit, $start);
        }

        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        $resourceArray = $object->toArray();

        //-- Get Settings Date and Time formats
        $dateFormat = $this->modx->getOption('mxcalendars.mgr_dateformat', '', 'm/d/Y');
        $timeFormat = $this->modx->getOption('mxcalendars.mgr_timeformat', '', 'g:i a');

        //-- Split the single unix time stamp into date and time for UI
        $resourceArray['startdate_date'] = date($dateFormat, $resourceArray['startdate']);
        $resourceArray['startdate_time'] = date($timeFormat, $resourceArray['startdate']);
        $resourceArray['enddate_date'] = date($dateFormat, $resourceArray['enddate']);
        $resourceArray['enddate_time'] = date($timeFormat, $resourceArray['enddate']);
        $ed = $resourceArray['repeatenddate'];
        $resourceArray['repeatenddate'] = !empty($ed) ? $resourceArray['repeatenddate'] : null;

        //-- Get the names of the Categories for friendly output
        $catFriendly = [];
        $curCatIds = $resourceArray['categoryid'];
        if (!empty($curCatIds)) {
            $cats = explode(',', $curCatIds);
            if (count($cats)) {
                foreach ($cats AS $cat) {
                    $obj = $this->modx->getObject('mxCalendarCategories', $cat);
                    if ($obj) {
                        $catFriendly[] = $obj->get('name');
                    }
                }
            }
        }
        $resourceArray['catfriendly'] = implode(', ', $catFriendly);

        if (false === $resourceArray['repeating']) {
            $resourceArray['repeating'] = 'ne';
        } else {
            $resourceArray['repeating'] = 'ano';
        }

        return $resourceArray;
    }

}
return 'EventsGetListProcessor';
