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
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\MatrixCol;
use rsanchez\Deep\Model\MatrixRow;

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

        $this->cols = MatrixCol::fieldId($fieldIds)->get();

        $collection->setMatrixCols($this->cols);
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        $this->rows = MatrixRow::entryId($entryIds)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(Entry $entry)
    {
        $cols = $this->cols;
        $rows = $this->rows;

        $entry->channel->fieldsByType($this->fieldtype)->each(function ($field) use ($entry, $rows, $cols) {

            $fieldCols = $cols->filter(function ($col) use ($field) {
                return $col->field_id === $field->field_id;
            });

            $fieldRows = $rows->filter(function ($row) use ($entry, $field) {
                return $entry->getKey() === $row->getKey() && $field->field_id === $row->field_id;
            })->each(function ($row) use ($fieldCols) {
                $row->setCols($fieldCols);

                $fieldCols->each(function ($col) use ($row) {
                    $row->setAttribute($col->col_name, $row->getAttribute('col_id_'.$col->col_id));
                });
            });

            $entry->setAttribute($field->field_name, $fieldRows);

        });
    }
}
