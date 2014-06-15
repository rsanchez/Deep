<?php

use rsanchez\Deep\Model\MemberField;
use rsanchez\Deep\Repository\MemberFieldRepository;

class MemberFieldRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \rsanchez\Deep\Repository\MemberFieldRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->repository = new MemberFieldRepository(new MemberField());
    }

    public function testGetFields()
    {
        $query = $this->repository->getFields();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\MemberFieldCollection', $query);
    }

    public function testGetFieldId()
    {
        $input = $this->repository->getFieldId('member_country');

        $this->assertEquals(1, $input);
    }

    public function testGetFieldName()
    {
        $input = $this->repository->getFieldName(1);

        $this->assertEquals('member_country', $input);
    }

    public function testHasField()
    {
        $input = $this->repository->hasField('member_country');

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
