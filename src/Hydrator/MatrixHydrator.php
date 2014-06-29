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
use rsanchez\Deep\Model\AbstractProperty;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Hydrator\AbstractHydrator;
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
     * {@inheritdoc}
     */
    public function __construct(EntryCollection $collection, $fieldtype)
    {
        parent::__construct($collection, $fieldtype);

        $fieldIds = $collection->getFieldIdsByFieldtype($fieldtype);

        $cols = MatrixCol::fieldId($fieldIds)->get();

        foreach ($cols as $col) {
            if (! isset($this->cols[$col->field_id])) {
                $this->cols[$col->field_id] = new MatrixColCollection();
            }

            $this->cols[$col->field_id]->push($col);
        }

        $collection->setMatrixCols($cols);
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        $rows = MatrixRow::entryId($entryIds)->orderBy('row_order', 'asc')->get();

        foreach ($rows as $row) {
            if (! isset($this->rows[$row->entry_id][$row->field_id])) {
                $this->rows[$row->entry_id][$row->field_id] = new MatrixRowCollection();
            }

            $cols = isset($this->cols[$row->field_id]) ? $this->cols[$row->field_id] : new MatrixColCollection();

            $row->setCols($cols);

            $this->rows[$row->entry_id][$row->field_id]->push($row);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $rows = isset($this->rows[$entity->getId()][$property->getId()]) ? $this->rows[$entity->getId()][$property->getId()] : new MatrixRowCollection();

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }
}
