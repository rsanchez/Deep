<?php

use rsanchez\Deep\Model\MatrixCol;

class MatrixColModelTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $query = MatrixCol::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\MatrixColCollection', $query);
    }

    public function testFieldIdScope()
    {
        $query = MatrixCol::fieldId(13)->get();

        $this->assertThat($query->fetch('col_id')->all(), new ArrayHasOnlyValuesConstraint([1, 2, 3]));
    }
}
