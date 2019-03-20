<?php

require_once (dirname(__DIR__).'/feed/update.class.php');

/**
 * Class FeedsUpdateFromGridProcessor
 *
 * Update Feed from the Grid
 *
 * @package mxcalendar
 * @subpackage processors
 */
class FeedsUpdateFromGridProcessor extends FeedsUpdateProcessor {
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
return 'FeedsUpdateFromGridProcessor';