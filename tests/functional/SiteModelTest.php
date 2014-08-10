<?php

use rsanchez\Deep\Model\Site;

class SiteModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \rsanchez\Deep\Model\Site
     */
    protected $site;

    public function setUp()
    {
        $this->site = Site::find(1);
    }

    public function testCollection()
    {
        $query = Site::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\SiteCollection', $query);
    }

    public function testGetSiteSystemPreferencesAttribute()
    {
        $this->assertInternalType('array', $this->site->site_system_preferences);
    }

    public function testGetSiteMemberPreferencesAttribute()
    {
        $this->assertInternalType('array', $this->site->site_member_preferences);
    }

    public function testGetSiteTemplatePreferencesAttribute()
    {
        $this->assertInternalType('array', $this->site->site_template_preferences);
    }

    public function testGetSitePagesAttribute()
    {
        $this->assertInternalType('array', $this->site->site_pages);
    }
}
