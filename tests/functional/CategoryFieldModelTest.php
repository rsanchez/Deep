<?php

use rsanchez\Deep\Model\CategoryField;

class CategoryFieldModelTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $query = CategoryField::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\CategoryFieldCollection', $query);
    }

    public function testGetFieldNameAttribute()
    {
        $field = CategoryField::find(1);

        $this->assertEquals('cat_color', $field->field_name);
    }

    public function testGetFieldIdAttribute()
    {
        $field = CategoryField::find(1);

        $this->assertEquals(1, $field->field_id);
    }
}
