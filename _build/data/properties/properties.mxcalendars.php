<?php
/**
 * Build the properties set.
 *
 * @package mxCalendar
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'theme',
        'desc' => 'prop_mxcalendars.desc_theme',
        'type' => 'textfield',
        'options' => '',
        'value' => 'default',
        'lexicon' => 'mxcalendars:properties',
    )
    
    //,array(
    //    'name' => 'detailId',
    //    'desc' => 'prop_mxcalendars.desc_detailid',
    //    'type' => 'textfield',
    //    'options' => '',
    //    'value' => 'resultWrapTpl',
    //    'lexicon' => 'mxcalendars:properties',
    //)
     ,array(
        'name' => 'displayType',
        'desc' => 'prop_mxcalendars.desc_displaytype',
        'type' => 'list',
        'options' => array(
            array('text' => 'prop_mxcalendars.lt_calendar','value' => 'calendar'),
            array('text' => 'prop_mxcalendars.lt_list','value' => 'list'),
            array('text' => 'prop_mxcalendars.lt_mini','value' => 'mini'),
        ),
        'value' => 'calendar',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'elStartDate',
        'desc' => 'prop_mxcalendars.desc_elstartdate',
        'type' => 'textfield',
        'options' => '',
        'value' => 'now',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'elEndDate',
        'desc' => 'prop_mxcalendars.desc_elenddate',
        'type' => 'textfield',
        'options' => '',
        'value' => '+4 weeks',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'eventListlimit',
        'desc' => 'prop_mxcalendars.desc_tpllistitem',
        'type' => 'textfield',
        'options' => '',
        'value' => '5',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'tplListItem',
        'desc' => 'prop_mxcalendars.desc_tpllistitem',
        'type' => 'textfield',
        'options' => '',
        'value' => 'tplListItem',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'tplListHeading',
        'desc' => 'prop_mxcalendars.desc_tpllistheading',
        'type' => 'textfield',
        'options' => '',
            'value' => 'tplListHeading',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'tplListWrap',
        'desc' => 'prop_mxcalendars.desc_tpllistwrap',
        'type' => 'textfield',
        'options' => '',
        'value' => 'tplListWrap',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'tplDetail',
        'desc' => 'prop_mxcalendars.desc_tpldetail',
        'type' => 'textfield',
        'options' => '',
        'value' => 'tplDetail',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'dateformat',
        'desc' => 'prop_mxcalendars.desc_dateformat',
        'type' => 'textfield',
        'options' => '',
        'value' => '%Y-%m-%d',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'timeformat',
        'desc' => 'prop_mxcalendars.desc_timeformat',
        'type' => 'textfield',
        'options' => '',
        'value' => '%H:%M %p',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'dateseperator',
        'desc' => 'prop_mxcalendars.desc_dateseperator',
        'type' => 'textfield',
        'options' => '',
        'value' => 'resultWrapTpl',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'activeMonthOnlyEvents',
        'desc' => 'prop_mxcalendars.desc_activemonthonlyevents',
        'type' => 'textfield',
        'options' => '',
        'value' => false,
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'hightlightToday',
        'desc' => 'prop_mxcalendars.desc_highlighttoday',
        'type' => 'textfield',
        'options' => '',
        'value' => true,
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'todayClass',
        'desc' => 'prop_mxcalendars.desc_todayclass',
        'type' => 'textfield',
        'options' => '',
        'value' => 'today',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'noEventClass',
        'desc' => 'prop_mxcalendars.desc_noeventclass',
        'type' => 'textfield',
        'options' => '',
            'value' => 'mxcDayNoEvents',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'hasEventsClass',
        'desc' => 'prop_mxcalendars.desc_haseventsclass',
        'type' => 'textfield',
        'options' => '',
            'value' => 'mxcEvents',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'tplEvent',
        'desc' => 'prop_mxcalendars.desc_tplevent',
        'type' => 'textfield',
        'options' => '',
            'value' => 'tplEvent',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'tplDay',
        'desc' => 'prop_mxcalendars.desc_tplday',
        'type' => 'textfield',
        'options' => '',
            'value' => 'tplDay',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'tplWeek',
        'desc' => 'prop_mxcalendars.desc_tplweek',
        'type' => 'textfield',
        'options' => '',
            'value' => 'tplWeek',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'tplMonth',
        'desc' => 'prop_mxcalendars.desc_tplmonth',
        'type' => 'textfield',
        'options' => '',
            'value' => 'tplMonth',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'tplHeading',
        'desc' => 'prop_mxcalendars.desc_tplheading',
        'type' => 'textfield',
        'options' => '',
            'value' => 'tplHeading',
        'lexicon' => 'mxcalendars:properties',
    ),array(
        'name' => 'debug',
        'desc' => 'prop_mxcalendars.desc_debug',
        'type' => 'textfield',
        'options' => '',
            'value' => false,
        'lexicon' => 'mxcalendars:properties',
    )
    
    //-- Category Properties
    ,array(
        'name' => 'showCategories',
        'desc' => 'prop_mxcalendars.desc_showCategories',
        'type' => 'textfield',
        'options' => '',
            'value' => true,
        'lexicon' => 'mxcalendars:properties',
    )
    ,array(
        'name' => 'tplCategoryWrap',
        'desc' => 'prop_mxcalendars.desc_tplCategoryWrap',
        'type' => 'textfield',
        'options' => '',
            'value' => 'tplCategoryWrap',
        'lexicon' => 'mxcalendars:properties',
    )
    ,array(
        'name' => 'tplCategoryItem',
        'desc' => 'prop_mxcalendars.desc_tplCategoryItem',
        'type' => 'textfield',
        'options' => '',
            'value' => 'tplCategoryItem',
        'lexicon' => 'mxcalendars:properties',
    )
    ,array(
        'name' => 'tplCategoryItem',
        'desc' => 'prop_mxcalendars.desc_tplCategoryItem',
        'type' => 'textfield',
        'options' => '',
            'value' => 'tplCategoryItem',
        'lexicon' => 'mxcalendars:properties',
    ),
    //--------------//
    
    array(
        'name' => 'sort',
        'desc' => 'prop_mxcalendars.sort_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'name',
        'lexicon' => 'mxcalendars:properties',
    ),
    array(
        'name' => 'dir',
        'desc' => 'prop_mxcalendars.dir_desc',
        'type' => 'list',
        'options' => array(
            array('text' => 'prop_mxcalendars.ascending','value' => 'ASC'),
            array('text' => 'prop_mxcalendars.descending','value' => 'DESC'),
        ),
        'value' => 'DESC',
        'lexicon' => 'mxcalendars:properties',
    ),
);
return $properties;