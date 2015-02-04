<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Model\AbstractField;

/**
 * Repository of all Fields
 */
abstract class AbstractFieldRepository extends AbstractRepository implements FieldRepositoryInterface
{
    /**
     * Array of Field keyed by field_name
     * @var array
     */
    protected $fieldsByName = [];

    /**
     * Array of Field keyed by field_id
     * @var array
     */
    protected $fieldsById = [];

    /**
     * Constructor
     *
     * @param \rsanchez\Deep\Model\AbstractField $model
     */
    public function __construct(AbstractField $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadCollection()
    {
        if (is_null($this->collection)) {
            parent::loadCollection();

            foreach ($this->collection as $field) {
                $this->fieldsByName[$field->field_name] = $field;
                $this->fieldsById[$field->field_id] = $field;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->getCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldId($field)
    {
        $this->loadCollection();

        return $this->fieldsByName[$field]->field_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName($id)
    {
        $this->loadCollection();

        return $this->fieldsById[$id]->field_name;
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($field)
    {
        $this->loadCollection();

        return isset($this->fieldsByName[$field]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasFieldId($id)
    {
        $this->loadCollection();

        return isset($this->fieldsById[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $this->loadCollection();

        return isset($this->fieldsById[$id]) ? $this->fieldsById[$id] : null;
    }
}
