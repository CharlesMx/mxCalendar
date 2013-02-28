<?php
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/mxcalendars.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    mxcCore.config = '.$modx->toJSON($mxcalendars->config).';
    mxcCore.siteId = \''.$modx->site_id.'\';
});
</script>');
return '<style>.valign-center{vertical-align:bottom;}</style>';

?>
