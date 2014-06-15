<?php

use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Repository\UploadPrefRepository;

class UploadPrefRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->repository = new UploadPrefRepository(new UploadPref());
    }

    public function testFind()
    {
        $uploadPref = $this->repository->find(1);

        $this->assertInstanceOf('\\rsanchez\\Deep\\Model\\UploadPref', $uploadPref);
    }

    public function testGetUploadPrefs()
    {
        $query = $this->repository->getUploadPrefs();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\UploadPrefCollection', $query);
    }
}
