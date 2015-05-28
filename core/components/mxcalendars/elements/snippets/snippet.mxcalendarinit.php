<?php
/**
 * mxCalendar initialization.
 * Run this snippet before using mxCalendar.
 *
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @return $mxCal mxCalendars
 */
$corePath = $modx->getOption('core_path');
$mxCalendarCorePath = $modx->getOption('mxcalendars.core_path',null,"{$corePath}components/mxcalendars/");
$mxCal = $modx->getService('mxcalendars','mxCalendars',$mxCalendarCorePath.'model/mxcalendars/',$scriptProperties);
if (!($mxCal instanceof mxCalendars)) throw new Exception('Error loading instance of mxCalendars.');
include_once "{$mxCalendarCorePath}processors/mgr/mxcHelper.php";
return $mxCal;