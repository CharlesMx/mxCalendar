<?php
$xpdo_meta_map['mxCalendarEventWUG']= array (
  'package' => 'mxcalendars',
  'table' => 'mxcalendars_wug',
  'fields' => 
  array (
    'eventid' => NULL,
    'webusergroup' => NULL,
  ),
  'fieldMeta' => 
  array (
    'eventid' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => false,
    ),
    'webusergroup' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'composites' => 
  array (
    'EventWUG' => 
    array (
      'class' => 'mxCalendarEvents',
      'local' => 'id',
      'foreign' => 'modUserGroup',
      'cardinality' => 'many',
    ),
    'EventId' => 
    array (
      'class' => 'mxCalendarEvents',
      'local' => 'eventid',
      'foreign' => 'id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
