<?php
$xpdo_meta_map['mxCalendarEventWUG']= array (
  'package' => 'mxcalendars',
  'version' => NULL,
  'table' => 'mxcalendars_wug',
  'extends' => 'xPDOSimpleObject',
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
    'WebUserGroup' => 
    array (
      'class' => 'mxCalendarEvents',
      'local' => 'webusergroup',
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
