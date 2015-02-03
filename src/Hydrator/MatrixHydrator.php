<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\MatrixCol;
use rsanchez\Deep\Model\MatrixRow;
use rsanchez\Deep\Collection\MatrixColCollection;
use rsanchez\Deep\Collection\MatrixRowCollection;

/**
 * Hydrator for the Matrix fieldtype
 */
class MatrixHydrator extends AbstractHydrator
{
    /**
     * @var \rsanchez\Deep\Model\MatrixCol
     */
    protected $colModel;

    /**
     * @var \rsanchez\Deep\Model\MatrixRow
     */
    protected $rowModel;

    /**
     * All Matrix cols used by this collection
     * @var \rsanchez\Deep\Collection\MatrixColCollection
     */
    protected $cols;

    /**
     * Collection of Matrix rows in this collection
     * @var \rsanchez\Deep\Collection\MatrixRowCollection
     */
    protected $rows;

    /**
     * Array of col collections sorted by field_id
     * @var array
     */
    protected $sortedCols = [];

    /**
     * Array of row collections sorted by entry_id and field_id
     * @var array
     */
    protected $sortedRows = [];

    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param string                                     $fieldtype
     * @param \rsanchez\Deep\Model\MatrixCol             $colModel
     * @param \rsanchez\Deep\Model\MatrixRow             $rowModel
     */
    public function __construct(HydratorCollection $hydrators, $fieldtype, MatrixCol $colModel, MatrixRow $rowModel)
    {
        parent::__construct($hydrators, $fieldtype);

        $this->colModel = $colModel;
        $this->rowModel = $rowModel;
    }

    /**
     * {@inheritdoc}
     */
    public function bootFromCollection(EntryCollection $collection)
    {
        $fieldIds = $collection->getFieldIdsByFieldtype($this->fieldtype);

        $this->cols = $this->colModel->fieldId($fieldIds)->orderBy('col_order')->get();

        foreach ($this->cols as $col) {
            if (! isset($this->sortedCols[$col->field_id])) {
                $this->sortedCols[$col->field_id] = new MatrixColCollection();
            }

            $this->sortedCols[$col->field_id]->push($col);
        }

        $collection->setMatrixCols($this->cols);
    }

    /**
     * {@inheritdoc}
     */
    public function preload(EntryCollection $collection)
    {
        $this->rows = $this->rowModel->entryId($collection->getEntryIds())->orderBy('row_order')->get();

        foreach ($this->rows as $row) {
            if (! isset($this->sortedRows[$row->entry_id][$row->field_id])) {
                $this->sortedRows[$row->entry_id][$row->field_id] = new MatrixRowCollection();
            }

            $cols = isset($this->sortedCols[$row->field_id]) ? $this->sortedCols[$row->field_id] : new MatrixColCollection();

            $row->setCols($cols);

            $this->sortedRows[$row->entry_id][$row->field_id]->push($row);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, PropertyInterface $property)
    {
        $entity->addCustomFieldSetter($property->getName(), [$this, 'setter']);

        if (isset($this->sortedRows[$entity->getId()][$property->getId()])) {
            $rows = $this->sortedRows[$entity->getId()][$property->getId()];
        } else {
            $rows = new MatrixRowCollection();
        }

        $rows->setProperty($property);

        foreach ($rows as $row) {
            foreach ($row->getCols() as $col) {
                $hydrator = $this->hydrators->get($col->getType());

                if ($hydrator) {
                    $value = $hydrator->hydrate($row, $col);
                } else {
                    $value = $row->{$col->getIdentifier()};
                }

                $row->setCustomField($col->getName(), $value);
            }
        }

        return $rows;
    }

    /**
     * Setter callback
     * @param  \rsanchez\Deep\Collection\MatrixRowCollection|array|null $value
     * @return \rsanchez\Deep\Collection\MatrixRowCollection|null
     */
    public function setter($value = null, PropertyInterface $property = null)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof MatrixRowCollection) {
            return $value;
        }

        if (is_array($value)) {
            $rows = new MatrixRowCollection();

            if ($property) {
                $rows->setProperty($property);
            }

            foreach ($value as $array) {
                $rows->addRow($array);
            }

            return $rows;
        }

        throw new \InvalidArgumentException('$value must be of type array, null, or \rsanchez\Deep\Collection\MatrixRowCollection.');
    }

    /**
     * Get rows preloaded by this hydrator
     *
     * @return \rsanchez\Deep\Collection\MatrixRowCollection
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Get cols preloaded by this hydrator
     *
     * @return \rsanchez\Deep\Collection\MatrixColCollection
     */
    public function getCols()
    {
        return $this->cols;
    }
}
