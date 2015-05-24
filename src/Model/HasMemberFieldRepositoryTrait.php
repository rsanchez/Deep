<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\MemberFieldRepository;

/**
 * Trait for possessing a MemberFieldRepository
 */
trait HasMemberFieldRepositoryTrait
{
    /**
     * Global Member Field Repository
     * @var \rsanchez\Deep\Repository\MemberFieldRepository
     */
    protected static $memberFieldRepository;

    /**
     * Set the global MemberFieldRepository
     * @param  \rsanchez\Deep\Repository\MemberFieldRepository $memberFieldRepository
     * @return void
     */
    public static function setMemberFieldRepository(MemberFieldRepository $memberFieldRepository)
    {
        static::$memberFieldRepository = $memberFieldRepository;
    }

    /**
     * Unset the global MemberFieldRepository
     * @return void
     */
    public static function unsetMemberFieldRepository()
    {
        static::$memberFieldRepository = null;
    }

    /**
     * Get the global MemberFieldRepository
     * @return \rsanchez\Deep\Repository\MemberFieldRepository
     * @throws \Exception
     */
    public static function getMemberFieldRepository()
    {
        if (! isset(static::$memberFieldRepository)) {
            throw new \Exception('The MemberFieldRepository is not set.');
        }

        return static::$memberFieldRepository;
    }
}
