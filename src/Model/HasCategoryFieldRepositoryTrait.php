<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\CategoryFieldRepositoryInterface;

/**
 * Trait for possessing a CategoryFieldRepository
 */
trait HasCategoryFieldRepositoryTrait
{
    /**
     * Global Category Field Repository
     * @var \rsanchez\Deep\Repository\CategoryFieldRepositoryInterface
     */
    protected static $categoryFieldRepository;

    /**
     * Set the global CategoryFieldRepository
     * @param  \rsanchez\Deep\Repository\CategoryFieldRepositoryInterface $categoryFieldRepository
     * @return void
     */
    public static function setCategoryFieldRepository(CategoryFieldRepositoryInterface $categoryFieldRepository)
    {
        static::$categoryFieldRepository = $categoryFieldRepository;
    }

    /**
     * Unset the global CategoryFieldRepository
     * @return void
     */
    public static function unsetCategoryFieldRepository()
    {
        static::$categoryFieldRepository = null;
    }

    /**
     * Get the global CategoryFieldRepository
     * @return \rsanchez\Deep\Repository\CategoryFieldRepositoryInterface
     */
    public static function getCategoryFieldRepository()
    {
        if (! isset(static::$categoryFieldRepository)) {
            throw new \Exception('The CategoryFieldRepository is not set.');
        }

        return static::$categoryFieldRepository;
    }
}
