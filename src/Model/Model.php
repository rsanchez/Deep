<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Validation\Factory as Validator;
use rsanchez\Deep\Exception\ValidationException;

/**
 * Abstract base model
 */
abstract class Model extends Eloquent
{
    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * Name of the connection to use for all Deep models
     * @var string
     */
    protected static $globalConnection;

    /**
     * @var \Illuminate\Validation\Factory
     */
    protected static $validator;

    /**
     * List of Illuminate\Validation rules
     * @var array
     */
    protected $rules = [];

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
     * Set the Validator factory instance for models
     * @param \Illuminate\Validation\Factory $validator
     */
    public static function setValidator(Validator $validator)
    {
        static::$validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * Override to validate if validator and rules are present
     *
     * @throws \rsanchez\Deep\Exception\ValidationException
     */
    public function save(array $options = [])
    {
        if (self::$validator && $this->rules) {
            $validator = self::$validator->make($this->attributes, $this->rules);

            if ($validator->fails()) {
                throw new ValidationException($validator->messages());
            }
        }

        return parent::save($options);
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
}
