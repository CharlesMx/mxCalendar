<?php

require_once (dirname(__DIR__).'/events/update.class.php');

/**
 * Class EventsUpdateFromGridProcessor
 *
 * Update Event item form Grid
 *
 * @package mxCalendars
 * @subpackage processors
 */
class EventsUpdateFromGridProcessor extends EventsUpdateProcessor {
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
return 'EventsUpdateFromGridProcessor';