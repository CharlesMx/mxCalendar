<?php
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.videos.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.images.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.categories.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.calendars.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/mxcalendars.feed.grid.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/widgets/home.panel.js');
$modx->regClientStartupScript($mxcalendars->config['jsUrl'].'mgr/sections/index.js');
 
/* If we want to use Tiny, we'll need some extra files. */
$useRTE = $modx->getOption('mxcalendars.use_richtext',$mxcalendars->config,false);
$whichEditor = $this->modx->getOption('which_editor');
$onRichTextEditorInit = '';

if ($useRTE) {

    if($whichEditor === 'TinyMCE'){
        $tinyCorePath = $modx->getOption('tiny.core_path',null,$modx->getOption('core_path').'components/tinymce/');
        if (file_exists($tinyCorePath.'tinymce.class.php')) {

            /* First fetch the mxcalendarstiny specific settings */
            $cb1 =  $modx->getOption('mxcalendars.tiny.buttons1');
            $cb2 =  $modx->getOption('mxcalendars.tiny.buttons2');
            $cb3 =  $modx->getOption('mxcalendars.tiny.buttons3');
            $cb4 =  $modx->getOption('mxcalendars.tiny.buttons4');
            $cb5 =  $modx->getOption('mxcalendars.tiny.buttons5');
            $plugins =  $modx->getOption('mxcalendars.tiny.custom_plugins');
            $theme =  $modx->getOption('mxcalendars.tiny.theme');
            $bfs =  $modx->getOption('mxcalendars.tiny.theme_advanced_blockformats');
            $css =  $modx->getOption('mxcalendars.tiny.theme_advanced_css_selectors');

            /* If the settings are empty, override them with the generic tinymce settings. */
            $tinyProperties = array(
                'height' => $modx->getOption('mxcalendars.tiny.height',null,200),
                'width' => $modx->getOption('mxcalendars.tiny.width',null,400),
                'tiny.custom_buttons1' => (!empty($cb1)) ? $cb1 : $modx->getOption('tiny.custom_buttons1'),
                'tiny.custom_buttons2' => (!empty($cb2)) ? $cb2 : $modx->getOption('tiny.custom_buttons2'),
                'tiny.custom_buttons3' => (!empty($cb3)) ? $cb3 : $modx->getOption('tiny.custom_buttons3'),
                'tiny.custom_buttons4' => (!empty($cb4)) ? $cb4 : $modx->getOption('tiny.custom_buttons4'),
                'tiny.custom_buttons5' => (!empty($cb5)) ? $cb5 : $modx->getOption('tiny.custom_buttons5'),
                'tiny.custom_plugins' => (!empty($plugins)) ? $plugins : $modx->getOption('tiny.custom_plugins'),
                'tiny.editor_theme' => (!empty($theme)) ? $theme : $modx->getOption('tiny.editor_theme'),
                'tiny.theme_advanced_blockformats' => (!empty($bfs)) ? $bfs : $modx->getOption('tiny.theme_advanced_blockformats'),
                'tiny.css_selectors' => (!empty($css)) ? $css : $modx->getOption('tiny.css_selectors'),
            );

            require_once $tinyCorePath.'tinymce.class.php';
            $tiny = new TinyMCE($modx,$tinyProperties);
            $tiny->setProperties($tinyProperties);
            $tiny->initialize();

            $modx->regClientStartupHTMLBlock('<script type="text/javascript">
                //delete Tiny.config.setup; // remove manager specific initialization code (depending on ModExt)
                Ext.onReady(function() {
                    MODx.loadRTE();
                });
            </script>');
        }
    } else {
        
        $rte_redactor = $this->modx->getOption('redactor.core_path',$config,$this->modx->getOption('core_path').'components/redactor/');
        require_once $rte_redactor.'model/redactor/redactor.class.php';
        $rte = new Redactor($modx);
        $rte->initialize();
        
        //$useEditor = $this->modx->getOption('use_editor');
        //$whichEditor = $this->modx->getOption('which_editor');
        if ($whichEditor == 'Redactor')
        {
            /* invoke OnRichTextEditorInit event */
            $onRichTextEditorInit = $this->modx->invokeEvent('OnRichTextEditorInit',array(
                'editor' => $whichEditor, // Not necessary for Redactor
                'elements' => array('cdescription'), // Not necessary for Redactor
            ));
            if (is_array($onRichTextEditorInit))
            {
                $onRichTextEditorInit = implode('', $onRichTextEditorInit);
            }
            $this->setPlaceholder('onRichTextEditorInit', $onRichTextEditorInit);
        }
        
        $rte_corePath = $this->modx->getOption('redactor.core_path',$config,$this->modx->getOption('core_path').'components/redactor/');
        $rte_assetsUrl = $this->modx->getOption('redactor.assets_url',$config,$this->modx->getOption('assets_url').'components/redactor/');
        $modx->regClientStartupScript($rte_assetsUrl.'redactor-1.2.3.min.js');
        $modx->regClientCSS($rte_assetsUrl.'redactor-1.2.3.min.css');
        
    }
      
}


return '<div id="mxcalendars-panel-home-div"></div><!-- RTE -->'.$onRichTextEditorInit.'<!-- End RTE -->';

?>
