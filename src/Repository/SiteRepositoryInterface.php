<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Collection\SiteCollection;
use rsanchez\Deep\Model\Site;

/**
 * Repository of all Sites
 */
interface SiteRepositoryInterface
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

    /**
     * Get the Page URI for the specified entry ID
     *
     * @param  int         $entryId
     * @return string|null
     */
    public function getPageUri($entryId);

    /**
     * Get all the entry IDs of entries that have Page URIs
     *
     * @return array
     */
    public function getPageEntryIds();

    /**
     * Get all the page uris of entries that have Page URIs
     *
     * @return array
     */
    public function getPageUris();
}
