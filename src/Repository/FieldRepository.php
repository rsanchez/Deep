<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Collection\FieldCollection;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Collection\ChannelCollection;

/**
 * Repository of all Fields
 */
class FieldRepository extends AbstractFieldRepository
{
    /**
     * Array of FieldCollection keyed by group_id
     * @var array
     */
    protected $fieldsByGroup = array();

    /**
     * {@inheritdoc}
     */
    public function __construct(Field $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    protected function boot()
    {
        if (is_null($this->collection)) {
            $this->collection = $this->model
                ->orderByRaw("field_type IN ('matrix', 'grid') DESC")
                ->orderByRaw("field_type IN ('playa', 'relationship') DESC")
                ->orderBy('field_order', 'asc')
                ->get();

            foreach ($this->collection as $field) {
                if (! array_key_exists($field->group_id, $this->fieldsByGroup)) {
                    $this->fieldsByGroup[$field->group_id] = new FieldCollection();
                }

                $this->fieldsByGroup[$field->group_id]->push($field);

                $this->fieldsByName[$field->field_name] = $field;
                $this->fieldsById[$field->field_id] = $field;
            }
        }
    }

    /**
     * Get a Collection of fields from the specified group
     *
     * @param  int                                       $groupId
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function getFieldsByGroup($groupId)
    {
        $this->boot();

        return $groupId && isset($this->fieldsByGroup[$groupId]) ? $this->fieldsByGroup[$groupId] : new FieldCollection();
    }

    /**
     * Get the fields used by the channels in the specified collection
     * @param  \rsanchez\Deep\Collection\ChannelCollection $channels
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function getFieldsByChannelCollection(ChannelCollection $channels)
    {
        $this->boot();

        $fields = new FieldCollection();

        foreach ($channels as $channel) {
            foreach ($channel->fields as $field) {
                $fields->push($field);
            }
        }

        return $fields;
    }
}
