<?php
$xpdo_meta_map['mxCalendarLog']= array (
  'package' => 'mxcalendars',
  'version' => NULL,
  'table' => 'mxcalendars_log',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'itemtype' => '',
    'log' => '',
    'datetime' => NULL,
  ),
  'fieldMeta' => 
  array (
    'itemtype' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '35',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'log' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'datetime' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => true,
    ),
  ),
);
