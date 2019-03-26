<?php

require_once (dirname(__DIR__).'/category/update.class.php');

/**
 * Class CategoriesUpdateFromGridProcessor
 *
 * Update Category from the Grid
 *
 * @package mxcalendar
 * @subpackage processors
 */
class CategoriesUpdateFromGridProcessor extends CategoriesUpdateProcessor {
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
return 'CategoriesUpdateFromGridProcessor';