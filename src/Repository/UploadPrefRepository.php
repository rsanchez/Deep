<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Collection\UploadPrefCollection;
use rsanchez\Deep\Model\UploadPref;

/**
 * Repository of all UploadPrefs
 */
class UploadPrefRepository extends AbstractDeferredRepository implements UploadPrefRepositoryInterface
{
    /**
     * Array of UploadPrefs keyed by id
     * @var array
     */
    protected $uploadPrefsById = array();

    /**
     * Constructor
     *
     * @param \rsanchez\Deep\Model\UploadPref $model
     */
    public function __construct(UploadPref $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    protected function boot()
    {
        parent::boot();

        foreach ($this->collection as $uploadPref) {
            $this->uploadPrefsById[$uploadPref->id] = $uploadPref;
        }
    }

    /**
     * Alias to getUploadPrefById
     * @var int $id
     * @return \rsanchez\Deep\Model\UploadPref|null
     */
    public function find($id)
    {
        return $this->getUploadPrefById($id);
    }

    /**
     * Get single UploadPref by ID
     * @var int $id
     * @return \rsanchez\Deep\Model\UploadPref|null
     */
    public function getUploadPrefById($id)
    {
        $this->boot();

        return array_key_exists($id, $this->uploadPrefsById) ? $this->uploadPrefsById[$id] : null;
    }

    /**
     * Get Collection of all UploadPrefs
     *
     * @return \rsanchez\Deep\Collection\UploadPrefCollection
     */
    public function getUploadPrefs()
    {
        $this->boot();

        return $this->collection;
    }
}
