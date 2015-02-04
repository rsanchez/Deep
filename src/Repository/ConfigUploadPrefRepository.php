<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Collection\UploadPrefCollection;

/**
 * Repository of all UploadPrefs
 */
class ConfigUploadPrefRepository extends UploadPrefRepository
{
    /**
     * Constructor
     *
     * @param \rsanchez\Deep\Model\UploadPref $model
     */
    public function __construct(UploadPref $model, array $config)
    {
        parent::__construct($model);

        $this->config = $config;
    }

    protected function loadCollection()
    {
        if (is_null($this->collection)) {
            parent::loadCollection();

            foreach ($this->collection as $uploadPref) {
                if (isset($this->config[$uploadPref->id])) {
                    foreach ($this->config[$uploadPref->id] as $key => $value) {
                        $uploadPref->$key = $value;
                    }
                }
            }
        }
    }
}
