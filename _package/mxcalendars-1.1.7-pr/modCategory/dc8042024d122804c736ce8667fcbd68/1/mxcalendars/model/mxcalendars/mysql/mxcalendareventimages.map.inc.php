<?php
$xpdo_meta_map['mxCalendarEventImages']= array (
  'package' => 'mxcalendars',
  'version' => NULL,
  'table' => 'mxcalendars_events_images',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'event_id' => NULL,
    'filepath' => '',
    'title' => '',
    'description' => '',
    'active' => 1,
  ),
  'fieldMeta' => 
  array (
    'event_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => false,
    ),
    'filepath' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'active' => 
    array (
      'dbtype' => 'boolean',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
  ),
);
