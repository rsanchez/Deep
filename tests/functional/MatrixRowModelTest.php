<?php

use rsanchez\Deep\Model\MatrixRow;
use rsanchez\Deep\Model\MatrixCol;

class MatrixRowModelTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $query = MatrixRow::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\MatrixRowCollection', $query);
    }

    public function testSetColsAttribute()
    {
        $row = MatrixRow::find(1);

        $cols = MatrixCol::fieldId(13)->get();

        $row->setCols($cols);

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\MatrixColCollection', $row->getCols());
    }

    public function testSetColsHidden()
    {
        $row = MatrixRow::find(1);

        $cols = MatrixCol::fieldId(13)->get();

        $row->setCols($cols);

        $array = $row->toArray();

        $this->assertArrayNotHasKey('col_id_1', $array);
    }

    public function testEntryIdScope()
    {
        $query = MatrixRow::entryId(8)->get();

        $this->assertThat($query->pluck('row_id')->all(), new ArrayHasOnlyValuesConstraint([2, 3]));
    }
}
