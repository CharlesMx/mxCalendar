<?php
$xpdo_meta_map['mxCalendarCalendars']= array (
  'package' => 'mxcalendars',
  'version' => NULL,
  'table' => 'mxcalendars_calendars',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'webusergroup' => NULL,
    'active' => 1,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'webusergroup' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
    ),
    'active' => 
    array (
      'dbtype' => 'boolean',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
  ),
  'aggregates' => 
  array (
    'CalendarId' => 
    array (
      'class' => 'mxCalendarEvents',
      'local' => 'id',
      'foreign' => 'calendar_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
