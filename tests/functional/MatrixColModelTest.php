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

        $ids = array_map(function ($model) {
            return $model->col_id;
        }, $query->all());

        $this->assertThat($ids, new ArrayHasOnlyValuesConstraint([1, 2, 3]));
    }
}
