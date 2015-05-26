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
class SiteRepository extends AbstractRepository implements SiteRepositoryInterface
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
     * {@inheritdoc}
     */
    public function getPageUri($entryId)
    {
        foreach ($this->getCollection() as $site) {
            if (isset($site->site_pages[$site->site_id]['uris'][$entryId])) {
                return $site->site_pages[$site->site_id]['uris'][$entryId];
            }
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageEntryIds()
    {
        $entryIds = [];

        foreach ($this->getCollection() as $site) {
            if (isset($site->site_pages[$site->site_id]['uris'])) {
                $entryIds = array_merge($entryIds, array_keys($site->site_pages[$site->site_id]['uris']));
            }
        }

        return $entryIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageUris()
    {
        $pageUris = [];

        foreach ($this->getCollection() as $site) {
            if (isset($site->site_pages[$site->site_id]['uris'])) {
                $pageUris += $site->site_pages[$site->site_id]['uris'];
            }
        }

        return $pageUris;
    }
}
