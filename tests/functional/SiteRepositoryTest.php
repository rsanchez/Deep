<?php

use rsanchez\Deep\Model\Site;
use rsanchez\Deep\Repository\SiteRepository;

class SiteRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->repository = new SiteRepository(new Site());
    }

    public function testGetSites()
    {
        $query = $this->repository->getSites();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\SiteCollection', $query);
    }

    public function testGetPageUri()
    {
        $pageUri = $this->repository->getPageUri(1);

        $this->assertEquals('/related-1', $pageUri);
    }

    public function testGetPageEntryIds()
    {
        $entryIds = $this->repository->getPageEntryIds();

        $this->assertThat($entryIds, new ArrayHasOnlyValuesConstraint([1, 3, 2, 6, 5, 4]));
    }
}
