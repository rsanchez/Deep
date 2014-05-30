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
interface RepositoryInterface
{
    /**
     * Find an entity by ID
     * @var int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id);
}
