<?php
require_once MODX_CORE_PATH . 'model/modx/modrequest.class.php';

class mxCalendarControllerRequest extends modRequest {
    public $mxcalendars = null;
    public $actionVar = 'action';
    public $defaultAction = 'index';
 
    function __construct(mxCalendars &$mxcalendars) {
        parent :: __construct($mxcalendars->modx);
        $this->mxcalendars =& $mxcalendars;
    }
 
    public function handleRequest() {
        $this->loadErrorHandler();
 
        /* save page to manager object. allow custom actionVar choice for extending classes. */
        $this->action = isset($_REQUEST[$this->actionVar]) ? $_REQUEST[$this->actionVar] : $this->defaultAction;
 
        $modx =& $this->modx;
        $mxcalendars =& $this->mxcalendars;
        $viewHeader = include $this->mxcalendars->config['corePath'].'controllers/mgr/header.php';
 
        $f = $this->mxcalendars->config['corePath'].'controllers/mgr/'.$this->action.'.php';
        if (file_exists($f)) {
            $viewOutput = include $f;
        } else {
            $viewOutput = 'Controller not found: '.$f;
        }
 
        return $viewHeader.$viewOutput;
    }
}

?>
