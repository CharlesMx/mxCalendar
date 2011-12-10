<?php


class mxCalendars {
    
    public $modx;
    public $config = array();
    
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
 
        $basePath = $this->modx->getOption('mxcalendars.core_path',$config,$this->modx->getOption('core_path').'components/mxcalendars/');
        $assetsUrl = $this->modx->getOption('mxcalendars.assets_url',$config,$this->modx->getOption('assets_url').'components/mxcalendars/');
        $this->config = array_merge(array(
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath.'model/',
            'processorsPath' => $basePath.'processors/',
            'chunksPath' => $basePath.'elements/chunks/',
            'jsUrl' => $assetsUrl.'js/',
            'cssUrl' => $assetsUrl.'css/',
            'assetsUrl' => $assetsUrl,
            'connectorUrl' => $assetsUrl.'connector.php',
        ),$config);
        $this->modx->addPackage('mxcalendars',$this->config['modelPath']);
    }
    
	/*
	 * MANAGER: Initialize the manager view for calendar item management
	 */
	public function initialize($ctx = 'web') {
	   switch ($ctx) {
			case 'mgr':
				$this->modx->lexicon->load('mxcalendars:default');
				if (!$this->modx->loadClass('mxcalendarControllerRequest',$this->config['modelPath'].'mxcalendars/request/',true,true)) {
				   return 'Could not load controller request handler. ['.$this->config['modelPath'].'mxcalendars/request/]';
				}
				$this->request = new mxcalendarControllerRequest($this);
				return $this->request->handleRequest();
			break;
		}
		return true;
	}
    
	/*
	 * GLOBAL HELPER FUNCTIONS: do what we can to making life easier
	 */ 
    public function getChunk($name,$properties = array()) {
		$chunk = null;
		if (!isset($this->chunks[$name])) {
			$chunk = $this->_getTplChunk($name);
			if (empty($chunk)) {
				$chunk = $this->modx->getObject('modChunk',array('name' => $name));
				if ($chunk == false) return false;
			}
			$this->chunks[$name] = $chunk->getContent();
		} else {
			$o = $this->chunks[$name];
			$chunk = $this->modx->newObject('modChunk');
			$chunk->setContent($o);
		}
		$chunk->setCacheable(false);
		return $chunk->process($properties);
	}
	 
	private function _getTplChunk($name,$postfix = '.chunk.tpl') {
		$chunk = false;
		$f = $this->config['chunksPath'].strtolower($name).$postfix;
		if (file_exists($f)) {
			$o = file_get_contents($f);
			$chunk = $this->modx->newObject('modChunk');
			$chunk->set('name',$name);
			$chunk->setContent($o);
		}
		return $chunk;
	}

}


?>
