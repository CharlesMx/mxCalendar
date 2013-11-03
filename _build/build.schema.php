<?php
require_once dirname(__FILE__).'/build.config.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->loadClass('transport.modPackageBuilder','',false, true);
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
 
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'model' => $root.'core/components/mxcalendars/model/',
    'schema_file' => $root.'core/components/mxcalendars/model/schema/mxcalendars.mysql.schema.xml',
);

// Clean the house before build a new one :)
$files = glob($sources['model'].'mxcalendars/mysql/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file)){
    unlink($file); // delete file
    echo 'Removed old: '.$file.'<br />';
  }
}
$files = glob($sources['model'].'mxcalendars/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file) && $file !== $sources['model'].'mxcalendars/mxcalendars.class.php' && $file !== $sources['model'].'mxcalendars/google_geoloc.class.inc.php'){
    unlink($file); // delete file
    echo 'Removed old: '.$file.'<br />';
  }
} 

$manager= $modx->getManager();
$generator= $manager->getGenerator();
 
if (!is_dir($sources['model'])) { $modx->log(modX::LOG_LEVEL_ERROR,'Model directory not found!'); die(); }
if (!file_exists($sources['schema_file'])) { echo "Schema FILE: ".$sources['schema_file'];$modx->log(modX::LOG_LEVEL_ERROR,'Schema file not found!'); die(); }
$generator->parseSchema($sources['schema_file'],$sources['model']);
 
echo 'Done.';
exit();
?>
