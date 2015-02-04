<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

/**
 * Repository of Models
 */
interface RepositoryInterface
{
    /**
     * Find an entity by ID
     * @var int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id);

    /**
     * Get the Collection of all items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCollection();

    /**
     * Get the Model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel();
}
