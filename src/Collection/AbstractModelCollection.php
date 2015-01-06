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
use rsanchez\Deep\Validation\ValidatableInterface;

/**
 * Collection of \Illuminate\Database\Eloquent\Model
 */
abstract class AbstractModelCollection extends Collection implements ValidatableInterface
{
    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->addModel($item);
    }

    /**
     * {@inheritdoc}
     */
    public function add($item)
    {
        $this->addModel($item);

        return $this;
    }

    /**
     * Add a Model to this collection
     * @param  \Illuminate\Database\Eloquent\Model $item
     * @return void
     */
    abstract public function addModel(Model $item);

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
    public function getValidationRules($prefix = '', $required = false)
    {
        $rules = [];

        if ($prefix && $required) {
            $rules[$prefix] = 'required';
        }

        foreach ($this->items as $i => $model) {
            $childPrefix = $prefix ? $prefix.'.'.$i : $i;

            $rules = array_merge($rules, $model->getValidationRules($childPrefix));
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
