<?php

require_once (dirname(__DIR__).'/images/update.class.php');

/**
 * Class EventImagesUpdateFromGridProcessor
 *
 * Update Event Image from the Grid
 *
 * @package mxcalendar
 * @subpackage processors
 */
class EventImagesUpdateFromGridProcessor extends EventImagesUpdateProcessor {
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
return 'EventImagesUpdateFromGridProcessor';