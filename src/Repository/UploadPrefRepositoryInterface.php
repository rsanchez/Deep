<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

/**
 * Repository of all UploadPrefs
 */
interface UploadPrefRepositoryInterface
{
    /**
     * Find an UploadPref model by ID
     * @var int $id
     * @return \rsanchez\Deep\Model\UploadPref|null
     */
    public function find($id);
}
