<?php

use rsanchez\Deep\Model\UploadPref;

class UploadPrefTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $query = UploadPref::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\UploadPrefCollection', $query);
    }
}
