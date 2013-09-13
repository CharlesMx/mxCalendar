<?php
$xpdo_meta_map['mxCalendarFeed']= array (
  'package' => 'mxcalendars',
  'version' => NULL,
  'table' => 'mxcalendars_feeds',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'feed' => '',
    'type' => '',
    'defaultcategoryid' => 0,
    'timerint' => 0,
    'timermeasurement' => '',
    'lastrunon' => 0,
    'nextrunon' => 0,
    'active' => 1,
  ),
  'fieldMeta' => 
  array (
    'feed' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'defaultcategoryid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'timerint' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'timermeasurement' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'lastrunon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'nextrunon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
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
    'eventfeed' => 
    array (
      'class' => 'mxCalendarEvents',
      'local' => 'id',
      'foreign' => 'feeds_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
