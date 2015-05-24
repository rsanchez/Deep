<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\SiteRepositoryInterface;

/**
 * Trait for possessing a SiteRepository
 */
trait HasSiteRepositoryTrait
{
    /**
     * Global Site Repository
     * @var \rsanchez\Deep\Repository\SiteRepositoryInterface
     */
    protected static $siteRepository;

    /**
     * Set the global SiteRepository
     * @param  \rsanchez\Deep\Repository\SiteRepositoryInterface $siteRepository
     * @return void
     */
    public static function setSiteRepository(SiteRepositoryInterface $siteRepository)
    {
        static::$siteRepository = $siteRepository;
    }

    /**
     * Unset the global SiteRepository
     * @return void
     */
    public static function unsetSiteRepository()
    {
        static::$siteRepository = null;
    }

    /**
     * Get the global SiteRepository
     * @return \rsanchez\Deep\Repository\SiteRepositoryInterface
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
