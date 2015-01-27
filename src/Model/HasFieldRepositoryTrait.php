<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\FieldRepository;

/**
 * Model for the channels table
 */
trait HasFieldRepositoryTrait
{
    /**
     * Global Field Repository
     * @var \rsanchez\Deep\Repository\FieldRepository
     */
    protected static $fieldRepository;

    /**
     * Set the global FieldRepository
     * @param  \rsanchez\Deep\Repository\FieldRepository $fieldRepository
     * @return void
     */
    public static function setFieldRepository(FieldRepository $fieldRepository)
    {
        static::$fieldRepository = $fieldRepository;
    }

    /**
     * Get the global FieldRepository
     * @return \rsanchez\Deep\Repository\FieldRepository
     */
    public static function getFieldRepository()
    {
        if (! isset(static::$fieldRepository)) {
            throw new \Exception('The FieldRepository is not set.');
        }

        return static::$fieldRepository;
    }
}
