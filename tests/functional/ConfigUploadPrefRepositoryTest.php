<?php

use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Repository\ConfigUploadPrefRepository;

class ConfigUploadPrefRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->repository = new ConfigUploadPrefRepository([
            1 => [
                'name' => 'Uploads',
                'server_path' => './uploads/',
                'url' => '/uploads/',
            ],
        ]);
    }

    public function testFind()
    {
        $uploadPref = $this->repository->find(1);

        $this->assertInstanceOf('\\rsanchez\\Deep\\Model\\UploadPref', $uploadPref);
    }
}
