<?php

use rsanchez\Deep\Model\GridCol;

class GridColModelTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $query = GridCol::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\GridColCollection', $query);
    }

    public function testFieldIdScope()
    {
        $query = GridCol::fieldId(12)->get();

        $this->assertThat($query->fetch('col_id')->all(), new ArrayHasOnlyValuesConstraint([1, 2, 3]));
    }
}
