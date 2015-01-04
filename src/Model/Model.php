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

/**
 * Abstract base model
 *
 * 1) Turns off Eloquent timestamps
 * 2) Ability to set global DB connection
 * 3) Self-validating if a validation factory is set
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
    protected static $validatorFactory;

    /**
     * List of validation messages
     * @var array
     */
    protected static $validationMessages = [
        'accepted' => 'The :attribute must be accepted.',
        'active_url' => 'The :attribute is not a valid URL.',
        'after' => 'The :attribute must be a date after :date.',
        'alpha' => 'The :attribute may only contain letters.',
        'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
        'alpha_num' => 'The :attribute may only contain letters and numbers.',
        'array' => 'The :attribute must be an array.',
        'before' => 'The :attribute must be a date before :date.',
        'between' => [
            'numeric' => 'The :attribute must be between :min and :max.',
            'file' => 'The :attribute must be between :min and :max kilobytes.',
            'string' => 'The :attribute must be between :min and :max characters.',
            'array' => 'The :attribute must have between :min and :max items.',
        ],
        'boolean' => 'The :attribute field must be true or false.',
        'confirmed' => 'The :attribute confirmation does not match.',
        'date' => 'The :attribute is not a valid date.',
        'date_format' => 'The :attribute does not match the format :format.',
        'different' => 'The :attribute and :other must be different.',
        'digits' => 'The :attribute must be :digits digits.',
        'digits_between' => 'The :attribute must be between :min and :max digits.',
        'email' => 'The :attribute must be a valid email address.',
        'exists' => 'The selected :attribute is invalid.',
        'image' => 'The :attribute must be an image.',
        'in' => 'The selected :attribute is invalid.',
        'integer' => 'The :attribute must be an integer.',
        'ip' => 'The :attribute must be a valid IP address.',
        'max' => [
            'numeric' => 'The :attribute may not be greater than :max.',
            'file' => 'The :attribute may not be greater than :max kilobytes.',
            'string' => 'The :attribute may not be greater than :max characters.',
            'array' => 'The :attribute may not have more than :max items.',
        ],
        'mimes' => 'The :attribute must be a file of type: :values.',
        'min' => [
            'numeric' => 'The :attribute must be at least :min.',
            'file' => 'The :attribute must be at least :min kilobytes.',
            'string' => 'The :attribute must be at least :min characters.',
            'array' => 'The :attribute must have at least :min items.',
        ],
        'not_in' => 'The selected :attribute is invalid.',
        'numeric' => 'The :attribute must be a number.',
        'regex' => 'The :attribute format is invalid.',
        'required' => 'The :attribute field is required.',
        'required_if' => 'The :attribute field is required when :other is :value.',
        'required_with' => 'The :attribute field is required when :values is present.',
        'required_with_all' => 'The :attribute field is required when :values is present.',
        'required_without' => 'The :attribute field is required when :values is not present.',
        'required_without_all' => 'The :attribute field is required when none of :values are present.',
        'same' => 'The :attribute and :other must match.',
        'size' => [
            'numeric' => 'The :attribute must be :size.',
            'file' => 'The :attribute must be :size kilobytes.',
            'string' => 'The :attribute must be :size characters.',
            'array' => 'The :attribute must contain :size items.',
        ],
        'unique' => 'The :attribute has already been taken.',
        'url' => 'The :attribute format is invalid.',
        'timezone' => 'The :attribute must be a valid zone.',
        /**/
        'url_title' => 'The :attribute must contain only letters, numbers, dashes and underscores.',
        /**/
        'custom' => [
            /*
            'attribute-name' => array(
                'rule-name' => 'custom-message',
            ),
            */
        ],
        'attributes' => [],
    ];

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
     * Add a default validation message
     * @param  string $name
     * @param  string $message
     * @return void
     */
    public static function addValidationMessage($name, $message)
    {
        static::$validationMessages[$name] = $message;
    }

    /**
     * Get the default validation messages
     * @return array
     */
    public static function getValidationMessages()
    {
        return static::$validationMessages;
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
        if (self::$validatorFactory && $this->rules) {
            $this->extendValidation(self::$validatorFactory);

            $validator = self::$validatorFactory->make($this->attributes, $this->rules);

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
