<?php

use rsanchez\Deep\Model\GridRow;
use rsanchez\Deep\Model\GridCol;

class GridRowModelTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $query = GridRow::from('channel_grid_field_12')->get();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\GridRowCollection', $query);
    }

    public function testSetColsAttribute()
    {
        $row = GridRow::from('channel_grid_field_12')->where('row_id', 1)->first();

        $cols = GridCol::fieldId(12)->get();

        $row->setCols($cols);

        $this->assertEquals('Text', $row->text);
    }

    public function testSetColsHidden()
    {
        $row = GridRow::from('channel_grid_field_12')->where('row_id', 1)->first();

        $cols = GridCol::fieldId(12)->get();

        $row->setCols($cols);

        $array = $row->toArray();

        $this->assertArrayNotHasKey('col_id_1', $array);
    }

    public function testEntryIdScope()
    {
        $query = GridRow::from('channel_grid_field_12')->entryId(8)->get();

        $this->assertThat($query->fetch('row_id')->all(), new ArrayHasOnlyValuesConstraint([2, 3]));
    }

    public function testFieldIdScope()
    {
        $query = GridRow::fieldId(12)->get();

        $this->assertThat($query->fetch('row_id')->all(), new ArrayHasOnlyValuesConstraint([1, 2, 3]));
    }
}
