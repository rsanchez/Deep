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

class TitleModelTest extends PHPUnit_Framework_TestCase
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

    public function testCategoryScopeSingle()
    {
        $entryIds = Title::category(1)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([7, 9]));
    }

    public function testCategoryScopeMultiple()
    {
        $entryIds = Title::category(1, 2)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([7, 9, 11]));
    }

    public function testRelatedCategoriesScope()
    {
        $entryIds = Title::relatedCategories(7)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([9, 11]));
    }

    public function testRelatedCategoriesUrlTitleScope()
    {
        $entryIds = Title::relatedCategoriesUrlTitle('entry-1')->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([9, 11]));
    }

    public function testAllCategoriesScope()
    {
        $entryIds = Title::allCategories(1, 2)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([7]));
    }

    public function testNotAllCategoriesScope()
    {
        $entryIds = Title::entryId(7, 9, 11)->notAllCategories(1, 2)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([9, 11]));
    }

    public function testCategoryNameScope()
    {
        $entryIds = Title::categoryName('category-a')->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([7, 9]));
    }

    public function testNotCategoryNameScope()
    {
        $entryIds = Title::entryId(7, 9, 11)->notCategoryName('category-a')->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([11]));
    }
}
