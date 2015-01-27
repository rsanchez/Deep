<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\CategoryFieldRepository;

/**
 * Trait for possessing a CategoryFieldRepository
 */
trait HasCategoryFieldRepositoryTrait
{
    /**
     * Global Category Field Repository
     * @var \rsanchez\Deep\Repository\CategoryFieldRepository
     */
    protected static $categoryFieldRepository;

    /**
     * Set the global CategoryFieldRepository
     * @param  \rsanchez\Deep\Repository\CategoryFieldRepository $categoryFieldRepository
     * @return void
     */
    public static function setCategoryFieldRepository(CategoryFieldRepository $categoryFieldRepository)
    {
        static::$categoryFieldRepository = $categoryFieldRepository;
    }

    /**
     * Get the global CategoryFieldRepository
     * @return \rsanchez\Deep\Repository\CategoryFieldRepository
     */
    public static function getCategoryFieldRepository()
    {
        if (! isset(static::$categoryFieldRepository)) {
            throw new \Exception('The CategoryFieldRepository is not set.');
        }

        return static::$categoryFieldRepository;
    }
}
