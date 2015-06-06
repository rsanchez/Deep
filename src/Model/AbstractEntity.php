<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Validation\Factory as ValidatorFactory;
use rsanchez\Deep\Validation\ProvidesValidationRulesInterface;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Validation\ValidatableInterface;
use rsanchez\Deep\Hydrator\AbstractHydratorFactory;
use rsanchez\Deep\Hydrator\HydratorCollection;
use rsanchez\Deep\Hydrator\DehydratorCollection;
use DateTime;

/**
 * Model for the channel entries, matrix rows and grid rows
 */
abstract class AbstractEntity extends Model
{
    /**
     * List of custom fields
     *   field_name => mixed value
     *
     * @var array
     */
    protected $customFields = [];

    /**
     * List of custom field setter closures
     *   field_name =>  closure
     *
     * @var array
     */
    protected $customFieldSetters = [];

    /**
     * Regex patterns of attributes to hide from toArray
     * @var string
     */
    protected $hiddenAttributesRegex;

    /**
     * Regex patterns of attributes to hide from toArray
     * @var string
     */
    protected $customFieldAttributesRegex;

    /**
     * List of attributes from additional table
     * @var array
     */
    protected $customFieldAttributes = [];

    /**
     * Global HydratorFactory
     * @var \rsanchez\Deep\Hydrator\AbstractHydratorFactory
     */
    protected static $hydratorFactory;

    /**
     * @var \rsanchez\Deep\Hydrator\DehydratorCollection
     */
    protected $dehydrators;

    /**
     * @var \rsanchez\Deep\Hydrator\HydratorCollection
     */
    protected $hydrators;

    /**
     * Set the global HydratorFactory
     * @param  \rsanchez\Deep\Hydrator\AbstractHydratorFactory $hydratorFactory
     * @return void
     */
    public static function setHydratorFactory(AbstractHydratorFactory $hydratorFactory)
    {
        static::$hydratorFactory = $hydratorFactory;
    }

    /**
     * get the global HydratorFactory
     * @return void
     */
    public static function getHydratorFactory()
    {
        if (! isset(static::$hydratorFactory)) {
            throw new \Exception('The HydratorFactory is not set.');
        }

        return static::$hydratorFactory;
    }

    /**
     * Set a custom field attribute
     * @param string $name  field/col/property name
     * @param mixed  $value
     */
    public function setCustomField($name, $value)
    {
        $this->customFields[$name] = $value;
    }

    /**
     * Get the raw custom field attributes
     * @return array
     */
    public function getCustomFieldAttributes()
    {
        return $this->customFieldAttributes;
    }

    /**
     * Set a raw custom field attribute
     * @return array
     */
    public function setCustomFieldAttribute($key, $value)
    {
        $this->customFieldAttributes[$key] = $value;
    }

    /**
     * Whether the specified raw custom field attribute is set
     * @return bool
     */
    public function hasCustomFieldAttribute($key)
    {
        return array_key_exists($key, $this->customFieldAttributes);
    }

    /**
     * Whether the specified key is a custom field attribute (e.g. field_id_X)
     * @return bool
     */
    public function isCustomFieldAttribute($key)
    {
        return !! preg_match($this->customFieldAttributesRegex, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        if ($this->customFieldAttributesRegex) {
            foreach ($attributes as $key => $value) {
                if ($this->isCustomFieldAttribute($key)) {
                    $this->setCustomFieldAttribute($key, $value);

                    unset($attributes[$key]);
                }
            }
        }

        parent::setRawAttributes($attributes, $sync);
    }

    /**
     * Register a custom field setter callback
     * @param  string   $name
     * @param  callable $setter
     * @return void
     */
    public function addCustomFieldSetter($name, callable $setter)
    {
        $this->customFieldSetters[$name] = $setter;
    }

    /**
     * {@inheritdoc}
     *
     * override to set custom field if $name matches a
     * current custom field
     */
    public function setAttribute($name, $value)
    {
        if (isset($this->customFieldSetters[$name])) {
            $this->customFields[$name] = call_user_func($this->customFieldSetters[$name], $value, $this->getProperties()->getPropertyByName($name));
        } elseif ($this->hasCustomFieldAttribute($name)) {
            $this->setCustomFieldAttribute($name, $value);
        } elseif ($this->isCustomFieldAttribute($name)) {
            $this->setCustomFieldAttribute($name, $value);
        } elseif (array_key_exists($name, $this->customFields)) {
            if ($this->customFields[$name] instanceof StringableInterface) {
                $this->customFields[$name]->setValue($value);
            } else {
                $this->customFields[$name] = $value;
            }
        } else {
            parent::setAttribute($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     *
     * override to get custom field if $name matches a
     * current custom field
     */
    public function getAttribute($name)
    {
        $customFieldAttributes = $this->getCustomFieldAttributes();

        if (array_key_exists($name, $customFieldAttributes)) {
            return $customFieldAttributes[$name];
        }

        if (array_key_exists($name, $this->customFields)) {
            return $this->customFields[$name];
        }

        return parent::getAttribute($name);
    }

    /**
     * Set the hydrators for this entry
     * @param \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     */
    public function setHydrators(HydratorCollection $hydrators)
    {
        $this->hydrators = $hydrators;
    }

    /**
     * Set the dehydrators for this entry
     * @param \rsanchez\Deep\Hydrator\DehydratorCollection $dehydrators
     */
    public function setDehydrators(DehydratorCollection $dehydrators)
    {
        $this->dehydrators = $dehydrators;
    }

    /**
     * Loop through all the custom fields and hydrate with empty data
     *
     * @return void
     */
    public function hydrateDefaultProperties()
    {
        $properties = $this->getProperties();

        if ($properties && ! $properties->isEmpty()) {
            $hydrators = static::getHydratorFactory()->getHydrators($properties);

            $this->setHydrators($hydrators);

            foreach ($properties as $property) {
                $name = $property->getName();

                $hydrator = $hydrators->get($property->getType());

                if ($hydrator) {
                    $this->setCustomField($name, $hydrator->hydrate($this, $property));
                } else {
                    $this->setCustomField($name, $this->{$property->getIdentifier()});
                }
            }
        }
    }

    /**
     * Convert a custom field property to an array/scalar
     * @param \rsanchez\Deep\Model\PropertyInterface $property
     * @return array|mixed|string
     */
    public function propertyToArray(PropertyInterface $property)
    {
        $value = $this->{$property->getName()};

        if (method_exists($value, 'toArray')) {
            return $value->toArray();
        } elseif (method_exists($value, '__toString')) {
            return (string) $value;
        } elseif (is_object($value)) {
            return (array) $value;
        } elseif ($this->isDataScalar($value)) {
            return $this->dataToScalar($value);
        }

        return $value;
    }

    /**
     * Get custom fields as an array
     * @return array
     */
    public function propertiesToArray()
    {
        $array = [];

        $self = $this;

        $this->getProperties()->each(function ($property) use (&$array, $self) {
            $array[$property->getName()] = $self->propertyToArray($property);
        });

        return $array;
    }

    /**
     * {@inheritdoc}
     *
     * override to 
     * 1) remove attributes that match $this->hiddenPatterns
     * 2) add custom fields to the resulting array
     */
    public function toArray()
    {
        $array = parent::toArray();

        if ($this->hiddenAttributesRegex) {
            foreach ($array as $key => $value) {
                if (preg_match($this->hiddenAttributesRegex, $key)) {
                    unset($array[$key]);
                }
            }
        }

        $array = array_merge($array, $this->propertiesToArray());

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatableAttributes()
    {
        $attributes = parent::getValidatableAttributes();

        foreach ($this->getProperties() as $property) {
            $value = $this->{$property->getName()};

            if ($value instanceof ValidatableInterface) {
                $attributes[$property->getName()] = $value->getValidatableAttributes();
            } elseif ($value instanceof DateTime) {
                $attributes[$property->getName()] = $value->format('U');
            } else {
                $attributes[$property->getName()] = $value;
            }
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeNames($prefix = '')
    {
        $names = parent::getAttributeNames($prefix);

        $prefix = $prefix ? rtrim($prefix, '.').'.' : '';

        foreach ($this->getProperties() as $property) {
            $names[$prefix.$property->getIdentifier()] = $property->getLabel();
            $names[$prefix.$property->getName()] = $property->getLabel();

            $value = $this->{$property->getName()};

            if ($value instanceof ProvidesValidationRulesInterface) {
                $names = array_merge($names, $value->getAttributeNames($prefix.$property->getIdentifier().'.'));
            }
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationRules(ValidatorFactory $validatorFactory, PropertyInterface $parentProperty = null)
    {
        $rules = parent::getValidationRules($validatorFactory, $parentProperty);

        foreach ($this->getProperties() as $property) {
            $value = $this->{$property->getName()};

            $propertyRules = [];

            if ($validatorFactory->hasPropertyValidator($property)) {
                $propertyRules = $validatorFactory->makePropertyValidator($property)->getRules($property);
            }

            if ($property->isRequired()) {
                array_unshift($propertyRules, 'required');
            }

            if ($propertyRules) {
                $rules[$property->getName()] = $propertyRules;
            }

            if ($value instanceof ProvidesValidationRulesInterface) {
                foreach ($value->getValidationRules($validatorFactory, $property) as $key => $val) {
                    $rules[$property->getName().'.'.$key] = $val;
                }
            }
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     *
     * Invoke any invokeable attributes
     */
    public function __call($name, $args)
    {
        if (isset($this->customFields[$name]) && is_callable($this->customFields[$name])) {
            return call_user_func_array($this->customFields[$name], $args);
        }

        return parent::__call($name, $args);
    }

    /**
     * Save the entry
     *
     * @param  array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $isNew = ! $this->exists;

        $saved = parent::save($options);

        if ($saved) {
            $this->saveCustomFields($isNew);
        }

        return $saved;
    }

    /**
     * Called after a successful save.
     * An opportunity to persist any additional custom field data to external tables
     * @param  bool $isNew whether the entity being saved is new or not
     * @return void
     */
    protected function saveCustomFields($isNew)
    {
    }

    /**
     * Get the entity ID (eg. entry_id or row_id)
     * @return string|int
     */
    abstract public function getId();

    /**
     * Get the entity type (eg. 'matrix' or 'grid' or 'entry')
     * @return string|null
     */
    abstract public function getType();

    /**
     * Get the entity prefix (eg. 'entry' or 'row')
     * @return string|null
     */
    abstract public function getPrefix();

    /**
     * Get collection of AbstractProperties
     * @return \rsanchez\Deep\Collection\PropertyCollection
     */
    abstract public function getProperties();
}
