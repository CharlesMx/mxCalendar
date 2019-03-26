<?php

require_once (dirname(__DIR__).'/videos/update.class.php');

/**
 * Class EventVideosUpdateFromGridProcessor
 *
 * Update Event Video from the Grid
 *
 * @package mxcalendar
 * @subpackage processors
 */
class EventVideosUpdateFromGridProcessor extends EventVideosUpdateProcessor {
    public function initialize() {
        $data = $this->getProperty('data');
        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }
        $data = $this->modx->fromJSON($data);
        //-- Both date and time are always posted back
        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }

        $data['updatefromgrid'] = 1;

        $this->setProperties($data);
        $this->unsetProperty('data');

        return parent::initialize();
    }

}
return 'EventVideosUpdateFromGridProcessor';