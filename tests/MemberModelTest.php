<?php

use rsanchez\Deep\Model\Member;
use rsanchez\Deep\Model\MemberField;
use rsanchez\Deep\Repository\MemberFieldRepository;

class MemberModelTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $memberFieldRepository = new MemberFieldRepository(new MemberField());

        Member::setMemberFieldRepository($memberFieldRepository);
    }

    public function testCollection()
    {
        $query = Member::all();

        $this->assertInstanceOf('\\Illuminate\\Database\\Eloquent\\Collection', $query);
    }

    public function testWithFieldsScope()
    {
        $member = Member::withFields()->first();

        $attributes = $member->getAttributes();

        $this->assertTrue(array_key_exists('m_field_id_1', $attributes));
    }

    public function testGetFieldAttribute()
    {
        $member = Member::withFields()->first();

        $input = $member->member_country;

        $this->assertEquals('USA', $input);
    }

    public function testToString()
    {
        $member = Member::find(1);

        $this->assertEquals($member->screen_name, (string) $member);
    }
}
