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
        $this->assertThat(Title::category(1)->get(), new CollectionNestedPropertyHasOneValueConstraint(1, 'categories', 'cat_id'));
    }

    public function testCategoryScopeMultiple()
    {
        $this->assertThat(Title::category(1, 2)->get(), new CollectionNestedPropertyHasOneValueConstraint([1, 2], 'categories', 'cat_id'));
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
        $this->assertThat(Title::allCategories(1, 2)->get(), new CollectionNestedPropertyHasAllValuesConstraint([1, 2], 'categories', 'cat_id'));
    }

    public function testNotAllCategoriesScope()
    {
        $this->assertThat(Title::notAllCategories(1, 2)->get(), new CollectionNestedPropertyDoesNotHaveAllValuesConstraint([1, 2], 'categories', 'cat_id'));
    }

    public function testCategoryNameScope()
    {
        $this->assertThat(Title::categoryName('category-a')->get(), new CollectionNestedPropertyHasOneValueConstraint('category-a', 'categories', 'cat_url_title'));
    }

    public function testNotCategoryNameScope()
    {
        $this->assertThat(Title::notCategoryName('category-a')->get(), new CollectionNestedPropertyDoesNotHaveAllValuesConstraint('category-a', 'categories', 'cat_url_title'));
    }

    public function testCategoryGroupScope()
    {
        $this->assertThat(Title::categoryGroup(1)->get(), new CollectionNestedPropertyHasOneValueConstraint(1, 'categories', 'group_id'));
    }

    public function testNotCategoryGroupScope()
    {
        $this->assertThat(Title::notCategoryGroup(1)->get(), new CollectionNestedPropertyDoesNotHaveAllValuesConstraint(1, 'categories', 'group_id'));
    }

    public function testAuthorIdScope()
    {
        $this->assertThat(Title::authorId(2)->get(), new CollectionPropertyHasOneValueConstraint(2, 'author_id'));
    }

    public function testNotAuthorIdScope()
    {
        $this->assertThat(Title::notAuthorId(2)->get(), new CollectionPropertyDoesNotHaveValueConstraint(2, 'author_id'));
    }

    public function testShowExpiredScopeFalse()
    {
        $this->assertThat(Title::showExpired(false)->get(), new CollectionPropertyCompareValueConstraint(time(), 'expiration_date', '<'));
    }

    public function testShowExpiredScope()
    {
        $entryIds = Title::showExpired(true)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasValueConstraint(9));
    }

    public function testShowFutureEntriesScopeFalse()
    {
        $entryIds = Title::showFutureEntries(false)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(10));
    }

    public function testShowFutureEntriesScope()
    {
        $entryIds = Title::showFutureEntries()->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasValueConstraint(10));
    }

    public function testSiteIdScope()
    {
        $query = Title::siteId(0)->get();

        $this->assertEquals(0, $query->count());
    }

    public function testFixedOrderScope()
    {
        $entryIds = Title::fixedOrder(7, 8, 9)->get()->fetch('entry_id')->all();

        $this->assertEquals([7, 8, 9], $entryIds);
    }

    public function testStickyScope()
    {
        $entry = Title::sticky()->first();

        $this->assertEquals(8, $entry->entry_id);
    }

    public function testEntryIdScope()
    {
        $entry = Title::entryId(1)->first();

        $this->assertEquals(1, $entry->entry_id);
    }

    public function testEntryIdFromScope()
    {
        $entryIds = Title::entryIdFrom(8)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(7));
    }

    public function testEntryIdToScope()
    {
        $entryIds = Title::entryIdTo(8)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(9));
    }

    public function testGroupIdScope()
    {
        $entryIds = Title::groupId(5)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([10]));
    }

    public function testNotGroupIdScope()
    {
        $entryIds = Title::notGroupId(5)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(10));
    }

    public function testOffsetScope()
    {
        //sqlite cannot do offset without limit
        $entryIds = Title::limit(99999)->offset(1)->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(1));
    }

    public function testScopeShowPages()
    {
        $channelIds = Title::showPages(false)->get()->fetch('channel_id')->all();

        $this->assertThat($channelIds, new ArrayDoesNotHaveValueConstraint(2));
    }

    public function testScopeShowPagesOnly()
    {
        $channelIds = Title::showPagesOnly()->get()->fetch('channel_id')->all();

        $this->assertThat($channelIds, new ArrayDoesNotHaveValueConstraint(1));
    }

    public function testScopeStartOn()
    {
        $entryIds = Title::startOn(strtotime('2014-01-01'))->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(9));
    }

    public function testScopeStartOnDateTime()
    {
        $entryIds = Title::startOn(DateTime::createFromFormat('Y-m-d', '2014-01-01'))->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(9));
    }

    public function testStatusScope()
    {
        $entryIds = Title::status('open')->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(11));
    }

    public function testNotStatusScope()
    {
        $entryIds = Title::notStatus('closed')->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(11));
    }

    public function testScopeStopBefore()
    {
        $entryIds = Title::stopBefore(strtotime('2014-12-31'))->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(10));
    }

    public function testScopeStopBeforeDateTime()
    {
        $entryIds = Title::stopBefore(DateTime::createFromFormat('Y-m-d', '2014-12-31'))->get()->fetch('entry_id')->all();

        $this->assertThat($entryIds, new ArrayDoesNotHaveValueConstraint(10));
    }
}
