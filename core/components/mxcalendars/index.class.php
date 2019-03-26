<?php
/**
 * mxCalendars
 *
 * Copyright 2019 by Lukas Smahel <ls@worldwebworks.cz>
 */
require_once dirname(__DIR__) . '/mxcalendars/model/mxcalendars/mxcalendars.class.php';
abstract class mxcalendarsBaseManagerController extends modExtraManagerController
{
    /**
     * @access public.
     * @return Mixed.
     */
    public $mxCalendars;

    public function initialize()
    {
        //$this->mxCalendars = new mxCalendars($this->modx);
        $this->mxCalendars = $this->modx->getService('mxCalendars', 'mxCalendars', $this->modx->getOption('mxcalendars.core_path', null, $this->modx->getOption('core_path') . 'components/mxcalendars/') . 'model/mxcalendars/');
        //return $this->mxCalendars->initialize('mgr');
        $this->addJavascript($this->mxCalendars->config['jsUrl'] . 'mgr/mxcalendars.js');

        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                mxcCore.config = ' . $this->modx->toJSON($this->mxCalendars->config) . ';
            });
            </script>');

        //var_dump($this->mxCalendars->config);
        /* If we want to use Tiny, we'll need some extra files. */
        $useRTE = $this->modx->getOption('mxcalendars.use_richtext', $this->mxCalendars->config, false);
        $whichEditor = $this->modx->getOption('which_editor');
        $onRichTextEditorInit = '';

        if ($useRTE) {

            if ($whichEditor === 'TinyMCE') {
                $tinyCorePath = $this->modx->getOption('tiny.core_path', null,
                    $this->modx->getOption('core_path') . 'components/tinymce/');
                if (file_exists($tinyCorePath . 'tinymce.class.php')) {

                    /* First fetch the mxcalendarstiny specific settings */
                    $cb1 = $this->modx->getOption('mxcalendars.tiny.buttons1');
                    $cb2 = $this->modx->getOption('mxcalendars.tiny.buttons2');
                    $cb3 = $this->modx->getOption('mxcalendars.tiny.buttons3');
                    $cb4 = $this->modx->getOption('mxcalendars.tiny.buttons4');
                    $cb5 = $this->modx->getOption('mxcalendars.tiny.buttons5');
                    $plugins = $this->modx->getOption('mxcalendars.tiny.custom_plugins');
                    $theme = $this->modx->getOption('mxcalendars.tiny.theme');
                    $bfs = $this->modx->getOption('mxcalendars.tiny.theme_advanced_blockformats');
                    $css = $this->modx->getOption('mxcalendars.tiny.theme_advanced_css_selectors');

                    /* If the settings are empty, override them with the generic tinymce settings. */
                    $tinyProperties = [
                        'height' => $this->modx->getOption('mxcalendars.tiny.height', null, 200),
                        'width' => $this->modx->getOption('mxcalendars.tiny.width', null, 400),
                        'tiny.custom_buttons1' => (!empty($cb1)) ? $cb1 : $this->modx->getOption('tiny.custom_buttons1'),
                        'tiny.custom_buttons2' => (!empty($cb2)) ? $cb2 : $this->modx->getOption('tiny.custom_buttons2'),
                        'tiny.custom_buttons3' => (!empty($cb3)) ? $cb3 : $this->modx->getOption('tiny.custom_buttons3'),
                        'tiny.custom_buttons4' => (!empty($cb4)) ? $cb4 : $this->modx->getOption('tiny.custom_buttons4'),
                        'tiny.custom_buttons5' => (!empty($cb5)) ? $cb5 : $this->modx->getOption('tiny.custom_buttons5'),
                        'tiny.custom_plugins' => (!empty($plugins)) ? $plugins : $this->modx->getOption('tiny.custom_plugins'),
                        'tiny.editor_theme' => (!empty($theme)) ? $theme : $this->modx->getOption('tiny.editor_theme'),
                        'tiny.theme_advanced_blockformats' => (!empty($bfs)) ? $bfs : $this->modx->getOption('tiny.theme_advanced_blockformats'),
                        'tiny.css_selectors' => (!empty($css)) ? $css : $this->modx->getOption('tiny.css_selectors'),
                    ];

                    require_once $tinyCorePath . 'tinymce.class.php';
                    $tiny = new TinyMCE($this->modx, $tinyProperties);
                    $tiny->setProperties($tinyProperties);
                    $tiny->initialize();

                    $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
                //delete Tiny.config.setup; // remove manager specific initialization code (depending on ModExt)
                Ext.onReady(function() {
                    MODx.loadRTE();
                });
            </script>');
                }
            } elseif ($whichEditor === 'Redactor') {
                /* invoke OnRichTextEditorInit event */
                $onRichTextEditorInit = $this->modx->invokeEvent('OnRichTextEditorInit', [
                    'editor' => $whichEditor, // Not necessary for Redactor
                    'elements' => ['cdescription'], // Not necessary for Redactor
                ]);
                if (is_array($onRichTextEditorInit)) {
                    $onRichTextEditorInit = implode('', $onRichTextEditorInit);
                }
                $this->modx->setPlaceholder('onRichTextEditorInit', $onRichTextEditorInit);
            }
        }

        parent::initialize();
    }

    /**
     * @access public
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('mxcalendars:default');
    }

    /**
     * @access public
     * @returns bool
     */
    public function checkPermissions()
    {
        return $this->modx->hasPermission('mxcalendars');
    }
}

class IndexManagerController extends mxcalendarsBaseManagerController
{
    /**
     * @access public.
     * @return String.
     */
    public static function getDefaultController()
    {
        return 'home';
    }
}
