<?php

use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Repository\FieldRepository;

class FieldRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \rsanchez\Deep\Repository\FieldRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->repository = new FieldRepository(new Field());
    }

    public function testGetFields()
    {
        $query = $this->repository->getFields();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\FieldCollection', $query);
    }

    public function testGetFieldId()
    {
        $input = $this->repository->getFieldId('assets');

        $this->assertEquals(1, $input);
    }

    public function testGetFieldName()
    {
        $input = $this->repository->getFieldName(1);

        $this->assertEquals('assets', $input);
    }

    public function testHasField()
    {
        $input = $this->repository->hasField('assets');

        $this->assertTrue($input);
    }

    public function testNotHasField()
    {
        $input = $this->repository->hasField('__bad_field__');

        $this->assertFalse($input);
    }

    public function testHasFieldId()
    {
        $input = $this->repository->hasFieldId(1);

        $this->assertTrue($input);
    }

    public function testNotHasFieldId()
    {
        $input = $this->repository->hasFieldId(99999);

        $this->assertFalse($input);
    }

    public function testGetFieldsByGroupCollection()
    {
        $query = $this->repository->getFieldsByGroup(1);

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\FieldCollection', $query);
    }

    public function testGetFieldsByGroupNoCollection()
    {
        $query = $this->repository->getFieldsByGroup(0);

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\FieldCollection', $query);
    }

    public function testGetFieldsByGroup()
    {
        $fields = $this->repository->getFieldsByGroup(1);

        $ids = [];

        foreach ($fields as $field) {
            $ids[] = $field->field_id;
        }

        $this->assertThat($ids, new ArrayHasOnlyValuesConstraint([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]));
    }

    public function testGetFieldsByChannelCollection()
    {
        $channels = Channel::where('channel_id', 1)->get();

        $fields = $this->repository->getFieldsByChannelCollection($channels);

        $ids = [];

        foreach ($fields as $field) {
            $ids[] = $field->field_id;
        }

        $this->assertThat($ids, new ArrayHasOnlyValuesConstraint([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]));
    }
}
