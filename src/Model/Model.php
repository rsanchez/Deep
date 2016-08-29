<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;
use rsanchez\Deep\Validation\Validator;
use rsanchez\Deep\Exception\ValidationException;
use rsanchez\Deep\Validation\ValidatableInterface;
use rsanchez\Deep\Validation\ProvidesValidationRulesInterface;

/**
 * Abstract base model
 *
 * 1) Turns off Eloquent timestamps
 * 2) Ability to set global DB connection
 * 3) Self-validating if a validation factory is set
 * 4) unguarded
 */
abstract class Model extends Eloquent implements ValidatableInterface, ProvidesValidationRulesInterface
{
    /**
     * {@inheritdoc}
     */
    protected static $unguarded = true;

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
     * Check if the specified data is scalar or castable to scalar
     * @param $data
     * @return bool
     */
    protected function isDataScalar($data)
    {
        return is_scalar($data) || $data instanceof StringableInterface || method_exists($data, '__toString');
    }

    /**
     * Convert the specified data to scalar, if castable, null otherwise
     * @param $data
     * @return null|string
     */
    protected function dataToScalar($data)
    {
        if (is_scalar($data)) {
            return $data;
        }

        if ($data instanceof StringableInterface) {
            return $data->getValue();
        }

        if (method_exists($data, '__toString')) {
            return (string) $data;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldValidateIfChild()
    {
        return ! $this->exists;
    }

    /**
     * Get validation rules for this model when saving an model
     * @param  \rsanchez\Deep\Validation\Factory          $validatorFactory
     * @param  \rsanchez\Deep\Model\PropertyInterface|null $property
     * @return array
     */
    protected function getDefaultValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        return $this->rules;
    }

    /**
     * Get validation rules for this model when updating existing
     * @param  \rsanchez\Deep\Validation\Factory          $validatorFactory
     * @param  \rsanchez\Deep\Model\PropertyInterface|null $property
     * @return array
     */
    public function getUpdateValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        return $this->getDefaultValidationRules($validatorFactory, $property);
    }

    /**
     * Get validation rules for this model when creating new
     * @param  \rsanchez\Deep\Validation\Factory          $validatorFactory
     * @param  \rsanchez\Deep\Model\PropertyInterface|null $property
     * @return array
     */
    public function getInsertValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        return $this->getDefaultValidationRules($validatorFactory, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        return $this->exists ? $this->getUpdateValidationRules($validatorFactory, $property) :  $this->getInsertValidationRules($validatorFactory, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatableAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set a raw model attribute. No checking is done.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function setRawAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Whether the specified attribute exists
     * @param  string $key
     * @return bool
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
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
        if (! static::$validatorFactory) {
            return true;
        }

        $this->extendValidation(static::$validatorFactory);

        $validator = static::$validatorFactory->make([], []);

        $rules = $this->getValidationRules(static::$validatorFactory);

        if (! $rules) {
            return true;
        }

        $validator->setRules($rules);

        $data = $this->getValidatableAttributes();

        $validator->setData($data);

        $attributeNames = $this->getAttributeNames();

        $validator->setAttributeNames($attributeNames);

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
     * Save the model to the database without validating
     *
     * @param  array  $options
     * @return bool
     */
    public function forceSave(array $options = [])
    {
        $options['validate'] = false;

        return $this->save($options);
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

    public function getRawAttribute($key)
    {
        return $this->getAttributeFromArray($key);
    }
}
