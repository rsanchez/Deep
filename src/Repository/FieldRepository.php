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
use rsanchez\Deep\Repository\AbstractFieldRepository;

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

    protected function boot()
    {
        if (is_null($this->collection)) {
            parent::boot();

            foreach ($this->collection as $field) {
                if (! array_key_exists($field->group_id, $this->fieldsByGroup)) {
                    $this->fieldsByGroup[$field->group_id] = new FieldCollection();
                }

                $this->fieldsByGroup[$field->group_id]->push($field);
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
}
