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
     * Find an entity by ID
     * @var int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id);

    /**
     * Get the Model
     * @return \rsanchez\Deep\Model\UploadPref
     */
    public function getModel();

    /**
     * Get the Collection of all items
     * @return \rsanchez\Deep\Collection\UploadPrefCollection
     */
    public function getCollection();

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
