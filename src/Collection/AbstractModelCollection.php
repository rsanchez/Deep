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
use rsanchez\Deep\Validation\ProvidesValidationRulesInterface;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Validation\Validator;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;

/**
 * Collection of \Illuminate\Database\Eloquent\Model
 */
abstract class AbstractModelCollection extends Collection implements ValidatableInterface, ProvidesValidationRulesInterface
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
