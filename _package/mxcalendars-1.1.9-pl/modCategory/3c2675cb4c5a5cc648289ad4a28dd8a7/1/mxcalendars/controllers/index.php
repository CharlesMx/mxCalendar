<?php

require_once dirname(dirname(__FILE__)).'/model/mxcalendars/mxcalendars.class.php';
$mxcalendars = new mxCalendars($modx);
return $mxcalendars->initialize('mgr');

?>
