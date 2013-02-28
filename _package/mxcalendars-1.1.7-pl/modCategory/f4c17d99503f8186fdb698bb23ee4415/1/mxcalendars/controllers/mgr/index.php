<?php
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.images.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.categories.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.calendars.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.feed.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/home.panel.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/sections/index.js');
 
return '<div id="mxcalendars-panel-home-div"></div>';

?>
