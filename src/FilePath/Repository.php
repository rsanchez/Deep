<?php

namespace rsanchez\Deep\FilePath;

use rsanchez\Deep\FilePath\FilePath;
use rsanchez\Deep\FilePath\Storage;
use rsanchez\Deep\FilePath\Factory;
use SplObjectStorage;

class Repository extends SplObjectStorage
{
    private $filePathsById = array();

    public function __construct(Storage $storage, Factory $factory)
    {
        foreach ($storage() as $row) {

            $filePath = $factory->createFilePath($row);

            $this->attach($filePath);
        }
    }

    public function attach(FilePath $filePath)
    {
        $this->filePathsById[$filePath->id] =& $filePath;
        return parent::attach($filePath);
    }

    public function find($id)
    {
        //@TODO custom exception
        if (! array_key_exists($id, $this->filePathsById)) {
            throw new \Exception('invalid channel id');
        }

        return $this->filePathsById[$id];
    }
}
