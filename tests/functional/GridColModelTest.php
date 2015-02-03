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

        $ids = array_map(function ($model) {
            return $model->col_id;
        }, $query->all());

        $this->assertThat($ids, new ArrayHasOnlyValuesConstraint([1, 2, 3]));
    }
}
