<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Validation\Factory as ValidatorFactory;
use rsanchez\Deep\Exception\ValidationException;
use rsanchez\Deep\Validation\ValidatableInterface;

/**
 * Abstract base model
 *
 * 1) Turns off Eloquent timestamps
 * 2) Ability to set global DB connection
 * 3) Self-validating if a validation factory is set
 */
abstract class Model extends Eloquent implements ValidatableInterface
{
    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * Whether this should validate on save
     * @var boolean
     */
    protected $shouldValidate = true;

    /**
     * Name of the connection to use for all Deep models
     * @var string
     */
    protected static $globalConnection;

    /**
     * @var \Illuminate\Validation\Factory
     */
    protected static $validatorFactory;

    /**
     * List of Illuminate\Validation rules
     * @var array
     */
    protected $rules = [];

    /**
     * List of attribute names/labels
     * @var array
     */
    protected $attributeNames = [];

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
     * Extend Validation to include any validateFoo methods on this model
     * @param  ValidatorFactory $validatorFactory
     * @return void
     */
    public function extendValidation(ValidatorFactory $validatorFactory)
    {
        $model = $this;

        $methods = get_class_methods($model);

        // look for validateFoo methods on this model
        foreach ($methods as $method) {
            if (! preg_match('/^validate([A-Z0-9].*?)$/', $method, $match)) {
                continue;
            }

            // extend only accepts Closures
            $validatorFactory->extend($match[1], function () use ($model, $method) {
                return call_user_func_array([$model, $method], func_get_args());
            });
        }
    }

    /**
     * Turn on validation on save
     * @return void
     */
    public function enableValidation()
    {
        $this->shouldValidate = true;
    }

    /**
     * Turn off validation on save
     * @return void
     */
    public function disableValidation()
    {
        $this->shouldValidate = false;
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
     * Set the ValidatorFactory instance for models
     * @param  \Illuminate\Validation\Factory $validatorFactory
     * @return void
     */
    public static function setValidatorFactory(ValidatorFactory $validatorFactory)
    {
        static::$validatorFactory = $validatorFactory;
    }

    /**
     * Unset the ValidatorFactory factory instance for models
     * @return void
     */
    public static function unsetValidatorFactory()
    {
        static::$validatorFactory = null;
    }

    /**
     * Get validation rules for this model when updating existing
     * @return array
     */
    public function getUpdateValidationRules()
    {
        return $this->rules;
    }

    /**
     * Get validation rules for this model when creating new
     * @return array
     */
    public function getInsertValidationRules()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationRules($prefix = '', $required = false)
    {
        $rules = $this->exists ? $this->getUpdateValidationRules() :  $this->getInsertValidationRules();

        if ($prefix) {

            $prefix = rtrim($prefix, '.');

            if ($required) {
                $rules[$prefix] = 'required';
            }

            foreach (array_keys($rules) as $key) {
                $rules[$prefix.'.'.$key] = $rules[$key];

                unset($rules[$key]);
            }
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatableAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function validateOrFail()
    {
        return $this->validate(true);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($exceptionOnFailure = false)
    {
        if (! self::$validatorFactory) {
            return true;
        }

        $rules = $this->getValidationRules();

        if (! $rules) {
            return true;
        }

        $this->extendValidation(self::$validatorFactory);

        $data = $this->getValidatableAttributes();

        $validator = self::$validatorFactory->make($data, $rules);

        $validator->setAttributeNames($this->getAttributeNames());

        if ($exceptionOnFailure && $validator->fails()) {
            throw new ValidationException($validator->messages());
        }

        return $validator->passes();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeNames($prefix = '')
    {
        $names = $this->attributeNames;

        if ($prefix) {
            $prefix = rtrim($prefix, '.').'.';

            foreach (array_keys($names) as $key) {
                $names[$prefix.$key] = $names[$key];

                unset($names[$key]);
            }
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     *
     * Override to validate if validatorFactory and rules are present
     *
     * @throws \rsanchez\Deep\Exception\ValidationException
     */
    public function save(array $options = [])
    {
        $disableValidation = isset($options['validate']) && $options['validate'] === false;

        if ($this->shouldValidate && ! $disableValidation) {
            $valid = $this->validateOrFail();

            if (! $valid) {
                return false;
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
