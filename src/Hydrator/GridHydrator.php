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
     * All Grid cols used by this collection
     * @var \rsanchez\Deep\Collection\GridColCollection
     */
    protected $cols;

    /**
     * Array of field_id => \rsanchez\Deep\Collection\GridRowCollection
     * @var array
     */
    protected $rows = array();

    /**
     * {@inheritdoc}
     */
    public function __construct(EntryCollection $collection, $fieldtype)
    {
        parent::__construct($collection, $fieldtype);

        $fieldIds = $collection->getFieldIdsByFieldtype($fieldtype);

        $cols = GridCol::fieldId($fieldIds)->get();

        foreach ($cols as $col) {
            if (! isset($this->cols[$col->field_id])) {
                $this->cols[$col->field_id] = new GridColCollection();
            }

            $this->cols[$col->field_id]->push($col);
        }

        $collection->setGridCols($cols);
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        $fieldIds = $this->collection->getFieldIdsByFieldtype($this->fieldtype);

        foreach ($fieldIds as $fieldId) {
            $rows = GridRow::fieldId($fieldId)->entryId($entryIds)->orderBy('row_order', 'asc')->get();

            foreach ($rows as $row) {
                if (! isset($this->rows[$row->entry_id][$row->field_id])) {
                    $this->rows[$row->entry_id][$row->field_id] = new GridRowCollection();
                }

                $cols = isset($this->cols[$row->field_id]) ? $this->cols[$row->field_id] : new GridColCollection();

                $row->setCols($cols);

                $this->rows[$row->entry_id][$row->field_id]->push($row);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $rows = isset($this->rows[$entity->getId()][$property->getId()]) ? $this->rows[$entity->getId()][$property->getId()] : new GridRowCollection();

        $entity->setAttribute($property->getName(), $value);
    }
}
