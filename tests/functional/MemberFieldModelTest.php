<?php

use rsanchez\Deep\Model\MemberField;

class MemberFieldModelTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $query = MemberField::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\MemberFieldCollection', $query);
    }

    public function testGetFieldNameAttribute()
    {
        $field = MemberField::find(1);

        $this->assertEquals('member_country', $field->field_name);
    }

    public function testGetFieldIdAttribute()
    {
        $field = MemberField::find(1);

        $this->assertEquals(1, $field->field_id);
    }
}
