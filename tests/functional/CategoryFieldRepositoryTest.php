<?php

use rsanchez\Deep\Model\CategoryField;
use rsanchez\Deep\Repository\CategoryFieldRepository;

class CategoryFieldRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \rsanchez\Deep\Repository\CategoryFieldRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->repository = new CategoryFieldRepository(new CategoryField());
    }

    public function testGetFields()
    {
        $query = $this->repository->getFields();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\CategoryFieldCollection', $query);
    }

    public function testGetFieldId()
    {
        $input = $this->repository->getFieldId('cat_color');

        $this->assertEquals(1, $input);
    }

    public function testGetFieldName()
    {
        $input = $this->repository->getFieldName(1);

        $this->assertEquals('cat_color', $input);
    }

    public function testHasField()
    {
        $input = $this->repository->hasField('cat_color');

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
}
