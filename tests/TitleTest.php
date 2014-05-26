<?php

use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Title;
use rsanchez\Deep\Model\Site;
use rsanchez\Deep\Model\Category;
use rsanchez\Deep\Model\Member;
use rsanchez\Deep\Model\CategoryField;
use rsanchez\Deep\Model\MemberField;
use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Repository\FieldRepository;
use rsanchez\Deep\Repository\ChannelRepository;
use rsanchez\Deep\Repository\SiteRepository;
use rsanchez\Deep\Repository\UploadPrefRepository;
use rsanchez\Deep\Repository\CategoryFieldRepository;
use rsanchez\Deep\Repository\MemberFieldRepository;
use rsanchez\Deep\Hydrator\HydratorFactory;

class TitleTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $fieldRepository = new FieldRepository(new Field());

        $categoryFieldRepository = new CategoryFieldRepository(new CategoryField());

        $memberFieldRepository = new MemberFieldRepository(new MemberField());

        $channelRepository = new ChannelRepository(new Channel(), $fieldRepository);

        $siteRepository = new SiteRepository(new Site());

        $uploadPrefRepository = new UploadPrefRepository(new UploadPref());

        $hydratorFactory = new HydratorFactory($siteRepository, $uploadPrefRepository);

        Category::setCategoryFieldRepository($categoryFieldRepository);
        Category::setChannelRepository($channelRepository);

        Member::setMemberFieldRepository($memberFieldRepository);

        Title::setChannelRepository($channelRepository);
        Title::setSiteRepository($siteRepository);
        Title::setHydratorFactory($hydratorFactory);
    }

    public function testCollection()
    {
        $query = Title::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\TitleCollection', $query);
    }

    public function testLimitScope()
    {
        $query = Title::limit(2)->get();

        $this->assertEquals(2, $query->count());
    }
}
