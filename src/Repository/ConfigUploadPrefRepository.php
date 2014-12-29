<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Model\UploadPref;

/**
 * Repository of all UploadPrefs
 */
class ConfigUploadPrefRepository implements UploadPrefRepositoryInterface
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
    public function __construct(array $uploadPrefs)
    {
        foreach ($uploadPrefs as $id => $config) {
            $uploadPref = new UploadPref();

            $uploadPref->name = $config['name'];
            $uploadPref->server_path = $config['server_path'];
            $uploadPref->url = $config['url'];

            $this->uploadPrefsById[$id] = $uploadPref;
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
        return array_key_exists($id, $this->uploadPrefsById) ? $this->uploadPrefsById[$id] : null;
    }
}
