<?php

use rsanchez\Deep\Model\File;
use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Collection\FieldCollection;
use rsanchez\Deep\Collection\EntryCollection;

class FileModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->file = File::find(1);

        $uploadPref = UploadPref::find($this->file->upload_location_id);

        $this->file->setUploadPref($uploadPref);
    }

    public function testCollection()
    {
        $query = File::all();

        $this->assertInstanceOf('\\rsanchez\\Deep\\Collection\\FileCollection', $query);
    }

    public function testFromEntryCollectionScope()
    {
        $field = new Field();

        $field->field_type = 'file';
        $field->field_id = 1;
        $field->field_name = 'file';

        $channel = new Channel();

        $channel->fields = new FieldCollection([$field]);

        $entry = new Entry();

        $entry->field_id_1 = '{filedir_1}1eecbed0063a0253.jpg';
        $entry->chan = $entry->channel = $channel;

        $entries = new EntryCollection([$entry]);

        $file = File::fromEntryCollection($entries)->first();

        $this->assertEquals(1, $file->file_id);
    }

    public function testGetUrlAttribute()
    {
        $this->assertEquals('/uploads/1eecbed0063a0253.jpg', $this->file->url);
    }

    public function testGetServerPathAttribute()
    {
        $this->assertEquals('./uploads/1eecbed0063a0253.jpg', $this->file->server_path);
    }

    public function testGetUploadDateAttribute()
    {
        $this->assertInstanceOf('\\Carbon\\Carbon', $this->file->upload_date);
    }

    public function testGetModifiedDateAttribute()
    {
        $this->assertInstanceOf('\\Carbon\\Carbon', $this->file->modified_date);
    }

    public function testGetHumanFileSizeAttribute()
    {
        $file = new File();

        $file->file_size = null;
        $this->assertEquals('0 B', $file->human_file_size);

        $file->file_size = 0;
        $this->assertEquals('0 B', $file->human_file_size);

        $file->file_size = 1023;
        $this->assertEquals('1023 B', $file->human_file_size);

        $file->file_size = 1024;
        $this->assertEquals('1 KB', $file->human_file_size);

        $file->file_size = 1234;
        $this->assertEquals('1.21 KB', $file->human_file_size);

        $file->file_size = 1048576;
        $this->assertEquals('1 MB', $file->human_file_size);

        $file->file_size = 1073741824;
        $this->assertEquals('1 GB', $file->human_file_size);

        $file->file_size = 1099511627776;
        $this->assertEquals('1 TB', $file->human_file_size);

        $file->file_size = 1125899906842624;
        $this->assertEquals('1 PB', $file->human_file_size);

        $file->file_size = 1152921504606846976;
        $this->assertEquals('1 EB', $file->human_file_size);

        $file->file_size = 1.1805916207175e21;
        $this->assertEquals('1 ZB', $file->human_file_size);

        $file->file_size = 1.2089258196147e24;
        $this->assertEquals('1 YB', $file->human_file_size);
    }

    public function testToString()
    {
        $this->assertEquals('/uploads/1eecbed0063a0253.jpg', $this->file->url);
    }
}
