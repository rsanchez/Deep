<?php

use rsanchez\Deep\Model\Field;

class FieldModelTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $query = Field::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\FieldCollection', $query);
    }

    public function testGetFieldNameAttribute()
    {
        $field = Field::find(1);

        $this->assertEquals('assets', $field->field_name);
    }

    public function testGetFieldIdAttribute()
    {
        $field = Field::where('field_name', 'assets')->first();

        $this->assertEquals(1, $field->field_id);
    }
}
