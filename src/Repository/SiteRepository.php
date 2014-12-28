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
class SiteRepository extends AbstractDeferredRepository
{
    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Model\Site $model
     */
    public function __construct(Site $model)
    {
        parent::__construct($model);
    }

    /**
     * Get Collection of all Sites
     *
     * @return \rsanchez\Deep\Collection\SiteCollection
     */
    public function getSites()
    {
        $this->boot();

        return $this->collection;
    }

    /**
     * Get the Page URI for the specified entry ID
     *
     * @param  int         $entryId
     * @return string|null
     */
    public function getPageUri($entryId)
    {
        foreach ($this->getSites() as $site) {
            if (isset($site->site_pages[$site->site_id]['uris'][$entryId])) {
                return $site->site_pages[$site->site_id]['uris'][$entryId];
            }
        }

        return;
    }

    /**
     * Get all the entry IDs of entries that have Page URIs
     *
     * @return array
     */
    public function getPageEntryIds()
    {
        $entryIds = array();

        foreach ($this->getSites() as $site) {
            if (isset($site->site_pages[$site->site_id]['uris'])) {
                $entryIds = array_merge($entryIds, array_keys($site->site_pages[$site->site_id]['uris']));
            }
        }

        return $entryIds;
    }
}
