<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Collection;

class EntryCollection extends Collection
{
    protected $matrixCols;
    protected $gridCols;

    /**
     * Map of fieldtypes to field IDs:
     *    'fieldtype_name' => array(1, 2, 3),
     * @var array
     */
    protected $fieldtypes = array();

    public function registerFieldtypes()
    {
        $this->fetch('channel.fields')->each(function ($rows) use (&$fieldtypes) {
            foreach ($rows as $row) {
                $this->fieldtypes[$row['field_type']][] = $row['field_id'];
            }
        });

        if ($this->hasFieldtype('matrix')) {
            $fieldIds = array_unique($this->fieldtypes['matrix']);

            $this->setMatrixCols(MatrixCol::fieldId($fieldIds)->get());
        }

        if ($this->hasFieldtype('grid')) {
            $fieldIds = array_unique($this->fieldtypes['grid']);

            $this->setGridCols(GridCol::fieldId($fieldIds)->get());
        }
    }

    public function hasFieldtype($fieldtype)
    {
        return array_key_exists($fieldtype, $this->fieldtypes);
    }

    public function getFieldIdsByFieldtype($fieldtype)
    {
        return isset($this->fieldtypes[$fieldtype]) ? $this->fieldtypes[$fieldtype] : array();
    }

    /**
     * Set the Matrix columns for this collection
     */
    public function setMatrixCols(Collection $matrixCols)
    {
        $this->matrixCols = $matrixCols;
    }

    public function getMatrixCols()
    {
        return $this->matrixCols;
    }

    public function setGridCols(Collection $gridCols)
    {
        $this->gridCols = $gridCols;
    }

    public function getGridCols()
    {
        return $this->gridCols;
    }
}
