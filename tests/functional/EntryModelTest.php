<?php

use rsanchez\Deep\Model\Model;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Title;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Model\Site;
use rsanchez\Deep\Model\Category;
use rsanchez\Deep\Model\Member;
use rsanchez\Deep\Model\CategoryField;
use rsanchez\Deep\Model\MemberField;
use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Model\Asset;
use rsanchez\Deep\Model\File;
use rsanchez\Deep\Model\GridCol;
use rsanchez\Deep\Model\GridRow;
use rsanchez\Deep\Model\MatrixCol;
use rsanchez\Deep\Model\MatrixRow;
use rsanchez\Deep\Model\PlayaEntry;
use rsanchez\Deep\Model\RelationshipEntry;
use rsanchez\Deep\Repository\FieldRepository;
use rsanchez\Deep\Repository\ChannelRepository;
use rsanchez\Deep\Repository\SiteRepository;
use rsanchez\Deep\Repository\UploadPrefRepository;
use rsanchez\Deep\Repository\CategoryFieldRepository;
use rsanchez\Deep\Repository\MemberFieldRepository;
use rsanchez\Deep\Hydrator\HydratorFactory;

class EntryModelTest extends PHPUnit_Framework_TestCase
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

        $hydratorFactory = new HydratorFactory(
            Model::resolveConnection(Model::getGlobalConnection()),
            $siteRepository,
            $uploadPrefRepository,
            new Asset(),
            new File(),
            new GridCol(),
            new GridRow(),
            new MatrixCol(),
            new MatrixRow(),
            new PlayaEntry(),
            new RelationshipEntry()
        );

        Category::setCategoryFieldRepository($categoryFieldRepository);
        Category::setChannelRepository($channelRepository);

        Member::setMemberFieldRepository($memberFieldRepository);

        Title::setChannelRepository($channelRepository);
        Title::setSiteRepository($siteRepository);
        Title::setHydratorFactory($hydratorFactory);

        Entry::setFieldRepository($fieldRepository);
    }

    public function testAssetHydration()
    {
        $entry = Entry::whereHas('chan', function ($query) {
            $query->whereNotNull('field_group');
        })->get()->first();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\AssetCollection', $entry->assets);
    }
}
