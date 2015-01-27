<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\SiteRepository;

/**
 * Trait for possessing a SiteRepository
 */
trait HasSiteRepositoryTrait
{
    /**
     * Global Site Repository
     * @var \rsanchez\Deep\Repository\SiteRepository
     */
    protected static $siteRepository;

    /**
     * Set the global SiteRepository
     * @param  \rsanchez\Deep\Repository\SiteRepository $siteRepository
     * @return void
     */
    public static function setSiteRepository(SiteRepository $siteRepository)
    {
        static::$siteRepository = $siteRepository;
    }

    /**
     * Get the global SiteRepository
     * @return \rsanchez\Deep\Repository\SiteRepository
     * @throws \Exception
     */
    public static function getSiteRepository()
    {
        if (! isset(static::$siteRepository)) {
            throw new \Exception('The SiteRepository is not set.');
        }

        return static::$siteRepository;
    }
}
