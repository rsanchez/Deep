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
interface UploadPrefRepositoryInterface extends RepositoryInterface
{
    /**
     * Get Collection of all UploadPrefs
     *
     * @return \rsanchez\Deep\Collection\UploadPrefCollection
     */
    public function getUploadPrefs();

    /**
     * Get single UploadPref by ID
     * @var int $id
     * @return \rsanchez\Deep\Model\UploadPref|null
     */
    public function getUploadPrefById($id);
}
