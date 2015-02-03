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
use rsanchez\Deep\Model\GridCol;
use rsanchez\Deep\Model\GridRow;
use rsanchez\Deep\Collection\GridColCollection;
use rsanchez\Deep\Collection\GridRowCollection;

/**
 * Hydrator for the Grid fieldtype
 */
class GridHydrator extends AbstractHydrator
{
    /**
     * @var \rsanchez\Deep\Model\GridCol
     */
    protected $colModel;

    /**
     * @var \rsanchez\Deep\Model\GridRow
     */
    protected $rowModel;

    /**
     * All Grid cols used by this collection
     * @var \rsanchez\Deep\Collection\GridColCollection
     */
    protected $cols;

    /**
     * Collection of Grid rows in this collection
     * @var \rsanchez\Deep\Collection\GridRowCollection
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
     * @param \rsanchez\Deep\Model\GridCol               $colModel
     * @param \rsanchez\Deep\Model\GridRow               $rowModel
     */
    public function __construct(HydratorCollection $hydrators, $fieldtype, GridCol $colModel, GridRow $rowModel)
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
                $this->sortedCols[$col->field_id] = new GridColCollection();
            }

            $this->sortedCols[$col->field_id]->push($col);
        }

        $collection->setGridCols($this->cols);
    }

    /**
     * {@inheritdoc}
     */
    public function preload(EntryCollection $collection)
    {
        $fieldIds = $collection->getFieldIdsByFieldtype($this->fieldtype);

        $this->rows = new GridRowCollection();

        foreach ($fieldIds as $fieldId) {
            $rows = $this->rowModel->fieldId($fieldId)->entryId($collection->getEntryIds())->orderBy('row_order')->get();

            foreach ($rows as $row) {
                if (! isset($this->sortedRows[$row->entry_id][$fieldId])) {
                    $this->sortedRows[$row->entry_id][$fieldId] = new GridRowCollection();
                }

                $cols = isset($this->sortedCols[$fieldId]) ? $this->sortedCols[$fieldId] : new GridColCollection();

                $row->setCols($cols);

                $this->sortedRows[$row->entry_id][$fieldId]->push($row);

                $this->rows->push($row);
            }
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
            $rows = new GridRowCollection();
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
     * @param  \rsanchez\Deep\Collection\GridRowCollection|array|null $value
     * @return \rsanchez\Deep\Collection\GridRowCollection|null
     */
    public function setter($value = null, PropertyInterface $property = null)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof GridRowCollection) {
            return $value;
        }

        if (is_array($value)) {
            $rows = new GridRowCollection();

            if ($property) {
                $rows->setProperty($property);
            }

            foreach ($value as $array) {
                $rows->addRow($array);
            }

            return $rows;
        }

        throw new \InvalidArgumentException('$value must be of type array, null, or \rsanchez\Deep\Collection\GridRowCollection.');
    }

    /**
     * Get rows preloaded by this hydrator
     *
     * @return \rsanchez\Deep\Collection\GridRowCollection
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Get cols preloaded by this hydrator
     *
     * @return \rsanchez\Deep\Collection\GridColCollection
     */
    public function getCols()
    {
        return $this->cols;
    }
}
