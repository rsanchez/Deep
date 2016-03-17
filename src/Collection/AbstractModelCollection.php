<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use rsanchez\Deep\Validation\ValidatableInterface;
use rsanchez\Deep\Validation\ProvidesValidationRulesInterface;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Validation\Validator;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;
use InvalidArgumentException;

/**
 * Collection of \Illuminate\Database\Eloquent\Model
 */
abstract class AbstractModelCollection extends Collection implements ValidatableInterface, ProvidesValidationRulesInterface
{
    /**
     * @var model class required by collection
     */
    protected $modelClass;

    /**
     * {@inheritdoc}
     */
    public function __construct($items = [])
    {
        $items = is_null($items) ? [] : $this->getArrayableItems($items);

        foreach ((array) $items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $item
     */
    protected function validateModel($item)
    {
        if ($this->modelClass && ! $item instanceof $this->modelClass) {
            throw new InvalidArgumentException("\$item must be an instance of {$this->modelClass}");
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $item
     */
    protected function prepareModel(Model $item)
    {
    }

    /**
     * Fetch a nested element of the collection.
     *
     * @param  string      $value
     * @param  string|null $key
     * @return Illuminate\Support\Collection
     */
    public function pluck($value, $key = null)
    {
        return new SupportCollection(array_pluck($this->toArray(), $value, $key));
    }

    /**
     * Fetch a nested element of the collection.
     *
     * @deprecated 2.0
     * @param  string  $key
     * @return Illuminate\Support\Collection
     */
    public function fetch($key)
    {
        return $this->pluck($key);
    }

    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->validateModel($item);

        $this->prepareModel($item);

        return parent::push($item);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend($item, $key = null)
    {
        $this->validateModel($item);

        $this->prepareModel($item);

        return parent::prepend($item, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function add($item)
    {
        $this->push($item);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeNames($prefix = '')
    {
        $names = [];

        $prefix = $prefix ? rtrim($prefix, '.').'.' : '';

        foreach ($this->items as $i => $model) {
            $names = array_merge($names, $model->getAttributeNames($prefix.$i));
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldValidateIfChild()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatableAttributes()
    {
        $attributes = [];

        foreach ($this->items as $model) {
            $attributes[] = $model->getValidatableAttributes();
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $property = null)
    {
        $rules = [];

        foreach ($this->items as $i => $model) {
            if ($model->shouldValidateIfChild()) {
                foreach ($model->getValidationRules($validatorFactory, $property) as $key => $value) {
                    $rules[$i . '.' . $key] = $value;
                }
            }
        }

        return $rules;
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
        $errors = [];

        foreach ($this->items as $i => $model) {
            try {
                $model->validateOrFail();
            } catch (ValidationException $e) {
                foreach ($e->getErrors() as $name => $error) {
                    $errors[$i.'.'.$name] = $error;
                }
            }
        }

        if ($exceptionOnFailure && $errors) {
            throw new ValidationException($errors);
        }

        return !! $errors;
    }
}
