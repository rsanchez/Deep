<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Abstract base model
 */
abstract class Model extends Eloquent
{
    /**
     * Name of the connection to use for all Deep models
     * @var string
     */
    protected static $globalConnection;

    /**
     * Set the global connection name for all Deep models
     * @param  string $connection
     * @return void
     */
    public static function setGlobalConnection($connection)
    {
        static::$globalConnection = $connection;
    }

    /**
     * Get the global connection name for all Deep models
     * @return string|null
     */
    public static function getGlobalConnection()
    {
        return static::$globalConnection;
    }

    /**
     * Get the database connection for the model.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        $connectionName = $this->connection ?: static::$globalConnection;

        return static::resolveConnection($connectionName);
    }

    /**
     * {@inheritdoc}
     */
    public static function hydrate(array $items, $connection = null)
    {
        $instance = (new static)->setConnection($connection);

        $items = array_map([$instance, 'newFromBuilder'], $items);

        return $instance->newCollection($items);
    }
}
