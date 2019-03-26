<?php
$xpdo_meta_map['mxCalendarEvents'] = [
    'package' => 'mxcalendars',
    'version' => null,
    'table' => 'mxcalendars_events',
    'extends' => 'xPDOSimpleObject',
    'fields' =>
        [
            'title' => '',
            'description' => '',
            'content' => '',
            'categoryid' => null,
            'link' => '',
            'linkrel' => '',
            'linktarget' => '',
            'location_name' => '',
            'location_address' => '',
            'map' => 0,
            'price' => '',
            'food' => '',
            'age' => '',
            'capacity' => 0,
            'allday' => 0,
            'startdate' => null,
            'enddate' => null,
            'repeating' => 0,
            'repeattype' => null,
            'repeaton' => null,
            'repeatfrequency' => null,
            'repeatenddate' => null,
            'repeatdates' => null,
            'source' => 'local',
            'feeds_id' => 0,
            'feeds_uid' => '',
            'lastedit' => null,
            'context' => '',
            'calendar_id' => 0,
            'form_chunk' => '',
            'createdon' => null,
            'createdby' => 0,
            'editedon' => null,
            'editedby' => 0,
            'active' => 1,
        ],
    'fieldMeta' =>
        [
            'title' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'description' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'content' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'categoryid' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '10',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'link' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'linkrel' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'linktarget' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'location_name' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'location_address' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'map' =>
                [
                    'dbtype' => 'boolean',
                    'phptype' => 'boolean',
                    'null' => false,
                    'default' => 0,
                ],
            'price' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'food' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'age' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'capacity' =>
                [
                    'dbtype' => 'int',
                    'precision' => '3',
                    'phptype' => 'integer',
                    'null' => true,
                ],
            'allday' =>
                [
                    'dbtype' => 'boolean',
                    'phptype' => 'boolean',
                    'null' => false,
                    'default' => 0,
                ],
            'startdate' =>
                [
                    'dbtype' => 'int',
                    'precision' => '20',
                    'phptype' => 'integer',
                    'null' => true,
                ],
            'enddate' =>
                [
                    'dbtype' => 'int',
                    'precision' => '20',
                    'phptype' => 'integer',
                    'null' => true,
                ],
            'repeating' =>
                [
                    'dbtype' => 'boolean',
                    'phptype' => 'boolean',
                    'null' => false,
                    'default' => 0,
                ],
            'repeattype' =>
                [
                    'dbtype' => 'int',
                    'precision' => '1',
                    'phptype' => 'integer',
                    'null' => true,
                ],
            'repeaton' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '15',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'repeatfrequency' =>
                [
                    'dbtype' => 'int',
                    'precision' => '2',
                    'phptype' => 'int',
                    'null' => true,
                ],
            'repeatenddate' =>
                [
                    'dbtype' => 'int',
                    'precision' => '20',
                    'phptype' => 'int',
                    'null' => true,
                ],
            'repeatdates' =>
                [
                    'dbtype' => 'text',
                    'phptype' => 'string',
                    'null' => true,
                ],
            'source' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '32',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => 'local',
                ],
            'feeds_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '20',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'feeds_uid' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => true,
                    'default' => '',
                ],
            'lastedit' =>
                [
                    'dbtype' => 'int',
                    'precision' => '20',
                    'phptype' => 'integer',
                    'null' => true,
                ],
            'context' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'calendar_id' =>
                [
                    'dbtype' => 'int',
                    'precision' => '20',
                    'phptype' => 'integer',
                    'null' => true,
                    'default' => 0,
                ],
            'form_chunk' =>
                [
                    'dbtype' => 'varchar',
                    'precision' => '255',
                    'phptype' => 'string',
                    'null' => false,
                    'default' => '',
                ],
            'createdon' =>
                [
                    'dbtype' => 'int',
                    'precision' => '20',
                    'phptype' => 'integer',
                    'null' => true,
                ],
            'createdby' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
                    'default' => 0,
                ],
            'editedon' =>
                [
                    'dbtype' => 'int',
                    'precision' => '20',
                    'phptype' => 'integer',
                    'null' => true,
                ],
            'editedby' =>
                [
                    'dbtype' => 'int',
                    'precision' => '10',
                    'attributes' => 'unsigned',
                    'phptype' => 'integer',
                    'null' => false,
                    'default' => 0,
                ],
            'active' =>
                [
                    'dbtype' => 'boolean',
                    'phptype' => 'boolean',
                    'null' => false,
                    'default' => 1,
                ],
        ],
    'composites' =>
        [
            'images' =>
                [
                    'class' => 'mxCalendarEventImages',
                    'local' => 'id',
                    'foreign' => 'event_id',
                    'cardinality' => 'many',
                ],
            'videos' =>
                [
                    'class' => 'mxCalendarEventVideos',
                    'local' => 'id',
                    'foreign' => 'event_id',
                    'cardinality' => 'many',
                ],
        ],
    'aggregates' =>
        [
            'CalendarId' =>
                [
                    'class' => 'mxCalendarCalendars',
                    'local' => 'calendar_id',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'eventfeed' =>
                [
                    'class' => 'mxCalendarFeed',
                    'local' => 'feeds_id',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'WebUserGroup' =>
                [
                    'class' => 'mxCalendarEventWUG',
                    'local' => 'id',
                    'foreign' => 'webusergroup',
                    'cardinality' => 'many',
                    'owner' => 'foreign',
                ],
            'CreatedBy' =>
                [
                    'class' => 'modUser',
                    'local' => 'createdby',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
            'EditedBy' =>
                [
                    'class' => 'modUser',
                    'local' => 'editedby',
                    'foreign' => 'id',
                    'cardinality' => 'one',
                    'owner' => 'foreign',
                ],
        ],
];
