<?php
$action = $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'mxcalendars',
    'parent' => 0,
    'controller' => 'controllers/index',
    'haslayout' => true,
    'lang_topics' => 'mxcalendars:default',
    'assets' => '',
), '', true, true);

$menu = $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'mxCalendar',
    'parent' => 'components',
    'description' => 'mxcalendars.desc',
    'icon' => 'images/icons/plugin.gif',
    'menuindex' => 0,
    'params' => '',
    'handler' => '',
), '', true, true);
$menu->addOne($action);
unset($menus);

return $menu;
