<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\UploadPrefRepository;

/**
 * Trait for possessing an UploadPrefRepository
 */
trait HasUploadPrefRepositoryTrait
{
    /**
     * @var \rsanchez\Deep\Repository\UploadPrefRepository
     */
    protected static $uploadPrefRepository;

    /**
     * Set the global UploadPrefRepository
     * @param  \rsanchez\Deep\Repository\UploadPrefRepository $uploadPrefRepository
     * @return void
     */
    public static function setUploadPrefRepository(UploadPrefRepository $uploadPrefRepository)
    {
        static::$uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * Get the global UploadPrefRepository
     * @return \rsanchez\Deep\Repository\UploadPrefRepository
     * @throws \Exception
     */
    public static function getUploadPrefRepository()
    {
        if (! isset(static::$uploadPrefRepository)) {
            throw new \Exception('The UploadPrefRepository is not set.');
        }

        return static::$uploadPrefRepository;
    }
}
