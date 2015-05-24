<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\FieldRepositoryInterface;

/**
 * Model for the channels table
 */
trait HasFieldRepositoryTrait
{
    /**
     * Global Field Repository
     * @var \rsanchez\Deep\Repository\FieldRepositoryInterface
     */
    protected static $fieldRepository;

    /**
     * Set the global FieldRepository
     * @param  \rsanchez\Deep\Repository\FieldRepositoryInterface $fieldRepository
     * @return void
     */
    public static function setFieldRepository(FieldRepositoryInterface $fieldRepository)
    {
        static::$fieldRepository = $fieldRepository;
    }

    /**
     * Unset the global FieldRepository
     * @return void
     */
    public static function unsetFieldRepository()
    {
        static::$fieldRepository = null;
    }

    /**
     * Get the global FieldRepository
     * @return \rsanchez\Deep\Repository\FieldRepositoryInterface
     */
    public static function getFieldRepository()
    {
        if (! isset(static::$fieldRepository)) {
            throw new \Exception('The FieldRepository is not set.');
        }

        return static::$fieldRepository;
    }
}
