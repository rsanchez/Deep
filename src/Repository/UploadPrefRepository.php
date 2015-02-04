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
class UploadPrefRepository extends AbstractRepository implements UploadPrefRepositoryInterface
{
    /**
     * Array of UploadPrefs keyed by id
     * @var array
     */
    protected $uploadPrefsById = [];

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
    protected function loadCollection()
    {
        if (is_null($this->collection)) {
            parent::loadCollection();

            foreach ($this->collection as $uploadPref) {
                $this->uploadPrefsById[$uploadPref->id] = $uploadPref;
            }
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
     * {@inheritdoc}
     */
    public function getUploadPrefById($id)
    {
        $this->loadCollection();

        return array_key_exists($id, $this->uploadPrefsById) ? $this->uploadPrefsById[$id] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadPrefs()
    {
        return $this->getCollection();
    }
}
