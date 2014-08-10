<?php

use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Repository\FieldRepository;

class ChannelModelTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $fieldRepository = new FieldRepository(new Field());

        Channel::setFieldRepository($fieldRepository);
    }

    public function setUp()
    {
        $this->all = Channel::all();
        $this->channel = Channel::find(1);
    }

    public function testCollection()
    {
        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\ChannelCollection', $this->all);
    }

    public function testFieldCollection()
    {
        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\FieldCollection', $this->channel->fields);
    }

    public function testFieldsAttribute()
    {
        $ids = [];

        foreach ($this->channel->fields as $field) {
            $ids[] = $field->field_id;
        }

        $this->assertThat($ids, new ArrayHasOnlyValuesConstraint([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]));
    }

    public function testFieldsByType()
    {
        $ids = [];

        foreach ($this->channel->fieldsByType('text') as $field) {
            $ids[] = $field->field_id;
        }

        $this->assertThat($ids, new ArrayHasOnlyValuesConstraint([19]));
    }

    public function testFieldsByTypeCollection()
    {
        $fields = $this->channel->fieldsByType('text');

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\FieldCollection', $fields);
    }

    public function testFieldsByTypeEmptyCollection()
    {
        $fields = $this->channel->fieldsByType('foo');

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\FieldCollection', $fields);
    }

    public function testFieldsByTypeEmpty()
    {
        $fields = $this->channel->fieldsByType('foo');

        $this->assertEquals(0, $fields->count());
    }

    public function testGetCatGroupAttribute()
    {
        $this->assertThat($this->channel->cat_group, new ArrayHasOnlyValuesConstraint([1, 2]));
    }

    public function testToString()
    {
        $this->assertEquals('entries', (string) $this->channel);
    }
}
