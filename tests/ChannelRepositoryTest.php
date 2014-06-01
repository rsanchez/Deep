<?php

use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Repository\ChannelRepository;
use rsanchez\Deep\Repository\FieldRepository;

class ChannelRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $fieldRepository = new FieldRepository(new Field());

        $this->repository = new ChannelRepository(new Channel(), $fieldRepository);
    }

    public function testGetChannelsByIdCollection()
    {
        $input = $this->repository->getChannelsById([1]);

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\ChannelCollection', $input);
    }

    public function testGetChannelsByNameCollection()
    {
        $input = $this->repository->getChannelsByName(['related']);

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\ChannelCollection', $input);
    }

    public function testGetChannelById()
    {
        $channel = $this->repository->getChannelById(1);

        $this->assertEquals('entries', $channel->channel_name);
    }

    public function testGetChannelByName()
    {
        $channel = $this->repository->getChannelByName('entries');

        $this->assertEquals(1, $channel->channel_id);
    }

    public function testGetModel()
    {
        $model = $this->repository->getModel();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Model\\Channel', $model);
    }
}
