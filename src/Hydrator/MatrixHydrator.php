<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Collection\EntryCollection;
use Illuminate\Database\ConnectionInterface;
use rsanchez\Deep\Model\AbstractProperty;
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
     * @param \Illuminate\Database\ConnectionInterface   $db
     * @param \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param string                                     $fieldtype
     * @param \rsanchez\Deep\Model\MatrixCol             $colModel
     * @param \rsanchez\Deep\Model\MatrixRow             $rowModel
     */
    public function __construct(ConnectionInterface $db, EntryCollection $collection, HydratorCollection $hydrators, $fieldtype, MatrixCol $colModel, MatrixRow $rowModel)
    {
        parent::__construct($db, $collection, $hydrators, $fieldtype);

        $this->colModel = $colModel;
        $this->rowModel = $rowModel;

        $fieldIds = $collection->getFieldIdsByFieldtype($fieldtype);

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
    public function preload(array $entryIds)
    {
        $this->rows = $this->rowModel->entryId($entryIds)->orderBy('row_order')->get();

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
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = isset($this->sortedRows[$entity->getId()][$property->getId()]) ? $this->sortedRows[$entity->getId()][$property->getId()] : new MatrixRowCollection();

        $entity->setAttribute($property->getName(), $value);

        return $value;
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
