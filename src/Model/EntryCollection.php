<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Collection;

class EntryCollection extends Collection
{
    protected $matrixCols;
    protected $gridCols;

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

    public function addGridCols(Collection $gridCols, $fieldId)
    {
        $this->gridCols[$fieldId] = $gridCols;
    }

    public function getGridCols($fieldId)
    {
        return $this->gridCols[$fieldId];
    }
}
