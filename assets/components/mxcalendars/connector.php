<?php
/**
 * mcCalendars Connector
 *
 * @package mcCalendars
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('mxcalendars.core_path', null, $modx->getOption('core_path') . 'components/mxcalendars/');

$modx->mxcalendars = $modx->getService(
    'mxCalendars',
    'mxCalendars',
    $corePath . 'model/mxcalendars/',
    array(
        'core_path' => $corePath
    )
);

/* handle request */
$modx->request->handleRequest(
    array(
        'processors_path' => $modx->getOption('processorsPath', null, $corePath . 'processors/'),
        'location' => '',
    )
);

$modx->lexicon->load('mxcalendars:default');
