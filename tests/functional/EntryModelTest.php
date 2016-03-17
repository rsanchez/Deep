<?php

use rsanchez\Deep\Model\Model;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Model\Channel;
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
use rsanchez\Deep\Hydrator\EntryHydratorFactory;

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

        $hydratorFactory = new EntryHydratorFactory(
            Model::resolveConnection(Model::getGlobalConnection()),
            $siteRepository,
            $uploadPrefRepository,
            new Asset(),
            new File(),
            new PlayaEntry(),
            new RelationshipEntry(),
            new GridCol(),
            new GridRow(),
            new MatrixCol(),
            new MatrixRow()
        );

        Category::setCategoryFieldRepository($categoryFieldRepository);
        Category::setChannelRepository($channelRepository);

        Member::setMemberFieldRepository($memberFieldRepository);

        Entry::setChannelRepository($channelRepository);
        Entry::setSiteRepository($siteRepository);
        Entry::setHydratorFactory($hydratorFactory);

        Entry::setFieldRepository($fieldRepository);
    }

    public function testCollection()
    {
        $query = Entry::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\EntryCollection', $query);
    }

    public function testLimitScope()
    {
        $query = Entry::limit(2)->get();

        $this->assertEquals(2, $query->count());
    }

    public function testCategoryScopeSingle()
    {
        $this->assertThat(Entry::category(1)->get(), new CollectionNestedCollectionPropertyHasOneValueConstraint(1, 'categories', 'cat_id'));
    }

    public function testCategoryScopeMultiple()
    {
        $this->assertThat(Entry::category(1, 2)->get(), new CollectionNestedCollectionPropertyHasOneValueConstraint([1, 2], 'categories', 'cat_id'));
    }

    public function testRelatedCategoriesScope()
    {
        $entryId = 7;

        $entry = Entry::entryId($entryId)->get()->first();

        $expected = [];

        foreach ($entry->categories as $category) {
            $expected[] = $category->cat_id;
        }

        $this->assertThat(Entry::relatedCategories($entryId)->get(), new CollectionNestedCollectionPropertyHasOneValueConstraint($expected, 'categories', 'cat_id'));
    }

    public function testRelatedCategoriesUrlTitleScope()
    {
        $urlTitle = 'entry-1';

        $entry = Entry::urlTitle($urlTitle)->get()->first();

        $expected = [];

        foreach ($entry->categories as $category) {
            $expected[] = $category->cat_id;
        }

        $this->assertThat(Entry::relatedCategoriesUrlTitle($urlTitle)->get(), new CollectionNestedCollectionPropertyHasOneValueConstraint($expected, 'categories', 'cat_id'));
    }

    public function testAllCategoriesScope()
    {
        $this->assertThat(Entry::allCategories(1, 2)->get(), new CollectionNestedCollectionPropertyHasAllValuesConstraint([1, 2], 'categories', 'cat_id'));
    }

    public function testNotAllCategoriesScope()
    {
        $this->assertThat(Entry::notAllCategories(1, 2)->get(), new CollectionNestedCollectionPropertyDoesNotHaveAllValuesConstraint([1, 2], 'categories', 'cat_id'));
    }

    public function testCategoryNameScope()
    {
        $this->assertThat(Entry::categoryName('category-a')->get(), new CollectionNestedCollectionPropertyHasOneValueConstraint('category-a', 'categories', 'cat_url_title'));
    }

    public function testNotCategoryNameScope()
    {
        $this->assertThat(Entry::notCategoryName('category-a')->get(), new CollectionNestedCollectionPropertyDoesNotHaveAllValuesConstraint('category-a', 'categories', 'cat_url_title'));
    }

    public function testCategoryGroupScope()
    {
        $this->assertThat(Entry::categoryGroup(1)->get(), new CollectionNestedCollectionPropertyHasOneValueConstraint(1, 'categories', 'group_id'));
    }

    public function testNotCategoryGroupScope()
    {
        $this->assertThat(Entry::notCategoryGroup(1)->get(), new CollectionNestedCollectionPropertyDoesNotHaveAllValuesConstraint(1, 'categories', 'group_id'));
    }

    public function testAuthorIdScope()
    {
        $this->assertThat(Entry::authorId(2)->get(), new CollectionPropertyHasOneValueConstraint(2, 'author_id'));
    }

    public function testNotAuthorIdScope()
    {
        $this->assertThat(Entry::notAuthorId(2)->get(), new CollectionPropertyDoesNotHaveValueConstraint(2, 'author_id'));
    }

    public function testShowExpiredScopeFalse()
    {
        $this->assertThat(Entry::showExpired(false)->where('expiration_date', '>', 0)->get(), new CollectionPropertyCompareDateTimeConstraint(time(), 'expiration_date', '>'));
    }

    public function testShowExpiredScope()
    {
        $this->assertThat(Entry::showExpired(true)->where('expiration_date', '>', 0)->where('expiration_date', '<', time())->get(), new CollectionPropertyCompareDateTimeConstraint(time(), 'expiration_date', '<'));
    }

    public function testShowFutureEntriesScopeFalse()
    {
        $this->assertThat(Entry::showFutureEntries(false)->get(), new CollectionPropertyCompareDateTimeConstraint(time(), 'entry_date', '<='));
    }

    public function testShowFutureEntriesScope()
    {
        $this->assertThat(Entry::showFutureEntries(true)->where('entry_date', '>', time())->get(), new CollectionPropertyCompareDateTimeConstraint(time(), 'entry_date', '>'));
    }

    public function testSiteIdScope()
    {
        $this->assertThat(Entry::siteId(1)->get(), new CollectionPropertyHasOneValueConstraint(1, 'site_id'));
    }

    public function testSiteIdInvalidScope()
    {
        $query = Entry::siteId(0)->get();

        $this->assertEquals(0, $query->count());
    }

    public function testFixedOrderScope()
    {
        $entryIds = Entry::fixedOrder(7, 8, 9)->get()->pluck('entry_id')->all();

        $this->assertEquals([7, 8, 9], $entryIds);
    }

    public function testStickyScope()
    {
        $this->assertThat(Entry::sticky()->limit(1)->get(), new CollectionPropertyHasOneValueConstraint('y', 'sticky'));
    }

    public function testEntryIdScope()
    {
        $this->assertThat(Entry::entryId(1)->get(), new CollectionPropertyHasOneValueConstraint(1, 'entry_id'));
    }

    public function testEntryIdFromScope()
    {
        $this->assertThat(Entry::entryIdFrom(8)->get(), new CollectionPropertyCompareValueConstraint(8, 'entry_id', '>='));
    }

    public function testEntryIdToScope()
    {
        $this->assertThat(Entry::entryIdTo(8)->get(), new CollectionPropertyCompareValueConstraint(8, 'entry_id', '<='));
    }

    public function testGroupIdScope()
    {
        $this->assertThat(Entry::groupId(5)->get(), new CollectionNestedPropertyHasOneValueConstraint(5, 'author', 'group_id'));
    }

    public function testNotGroupIdScope()
    {
        $this->assertThat(Entry::notGroupId(5)->get(), new CollectionNestedPropertyDoesNotHaveValueConstraint(5, 'author', 'group_id'));
    }

    public function testOffsetScope()
    {
        //sqlite cannot do offset without limit
        $a = Entry::limit(100)->get();
        $b = Entry::limit(100)->offset(1)->get();

        $this->assertEquals($a->count() - 1, $b->count());
    }

    public function testScopeShowPages()
    {
        $pageUris = [];

        Entry::showPages()->get()->each(function ($entry) use (&$pageUris) {
            $pageUris[] = $entry->page_uri;
        });

        // remove null page uris
        $pageUris = array_filter($pageUris);

        $this->assertGreaterThan(0, count($pageUris));
    }

    public function testScopeShowPagesFalse()
    {
        $this->assertThat(Entry::showPages(false)->get(), new CollectionPropertyCompareValueConstraint(null, 'page_uri'));
    }

    public function testScopeShowPagesOnly()
    {
        $pageUris = [];

        Entry::showPagesOnly()->get()->each(function ($entry) use (&$pageUris) {
            $pageUris[] = $entry->page_uri;
        });

        $this->assertContainsOnly('string', $pageUris);
    }

    public function testScopeStartOn()
    {
        $this->assertThat(Entry::startOn(strtotime('2014-01-01'))->get(), new CollectionPropertyCompareDateTimeConstraint(DateTime::createFromFormat('Y-m-d', '2014-01-01'), 'entry_date', '>'));
    }

    public function testScopeStartOnDateTime()
    {
        $this->assertThat(Entry::startOn(DateTime::createFromFormat('Y-m-d', '2014-01-01'))->get(), new CollectionPropertyCompareDateTimeConstraint(DateTime::createFromFormat('Y-m-d', '2014-01-01'), 'entry_date', '>'));
    }

    public function testStatusScope()
    {
        $this->assertThat(Entry::status('open')->get(), new CollectionPropertyHasOneValueConstraint('open', 'status'));
    }

    public function testNotStatusScope()
    {
        $this->assertThat(Entry::notStatus('closed')->get(), new CollectionPropertyDoesNotHaveValueConstraint('closed', 'status'));
    }

    public function testScopeStopBefore()
    {
        $this->assertThat(Entry::stopBefore(strtotime('2014-12-31'))->get(), new CollectionPropertyCompareDateTimeConstraint(DateTime::createFromFormat('Y-m-d', '2014-12-31'), 'entry_date', '<'));
    }

    public function testScopeStopBeforeDateTime()
    {
        $this->assertThat(Entry::stopBefore(DateTime::createFromFormat('Y-m-d', '2014-12-31'))->get(), new CollectionPropertyCompareDateTimeConstraint(DateTime::createFromFormat('Y-m-d', '2014-12-31'), 'entry_date', '<'));
    }

    public function testUsernameScope()
    {
        $this->assertThat(Entry::username('admin')->get(), new CollectionPropertyHasOneValueConstraint('admin', 'username'));
    }

    public function testYearScope()
    {
        $this->assertThat(Entry::year(2014)->get(), new CollectionPropertyHasOneValueConstraint('2014', 'year'));
    }

    public function testMonthScope()
    {
        $this->assertThat(Entry::month(5)->get(), new CollectionPropertyHasOneValueConstraint('05', 'month'));
    }

    public function testDayScope()
    {
        $this->assertThat(Entry::day(8)->get(), new CollectionPropertyHasOneValueConstraint('08', 'day'));
    }

    public function testAssetHydration()
    {
        $entry = Entry::whereHas('chan', function ($query) {
            $query->whereNotNull('field_group');
        })->get()->first();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\AssetCollection', $entry->assets);
    }
}
