<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;

/**
 * Trait for possessing an UploadPrefRepository
 */
trait HasUploadPrefRepositoryTrait
{
    /**
     * @var \rsanchez\Deep\Repository\UploadPrefRepositoryInterface
     */
    protected static $uploadPrefRepository;

    /**
     * Set the global UploadPrefRepository
     * @param  \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     * @return void
     */
    public static function setUploadPrefRepository(UploadPrefRepositoryInterface $uploadPrefRepository)
    {
        static::$uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * Unset the global UploadPrefRepository
     * @return void
     */
    public static function unsetUploadPrefRepository()
    {
        static::$uploadPrefRepository = null;
    }

    /**
     * Get the global UploadPrefRepository
     * @return \rsanchez\Deep\Repository\UploadPrefRepositoryInterface
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
