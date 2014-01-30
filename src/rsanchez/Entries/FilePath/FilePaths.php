<?php

namespace rsanchez\Entries\FilePath;

use rsanchez\Entries\FilePath\FilePath;
use rsanchez\Entries\FilePath\Storage;
use rsanchez\Entries\FilePath\Factory;
use IteratorAggregate;

class FilePaths implements IteratorAggregate
{
    private $filePaths = array();
    private $filePathsById = array();

    public function __construct(Storage $storage, Factory $factory)
    {
        foreach ($storage() as $row) {

            $filePath = $factory->createFilePath($row);

            $this->push($filePath);
        }
    }

    public function push(FilePath $filePath)
    {
        array_push($this->filePaths, $filePath);
        $this->filePathsById[$filePath->id] =& $filePath;
    }

    public function find($id)
    {
        //@TODO custom exception
        if (! array_key_exists($id, $this->filePathsById)) {
            throw new \Exception('invalid channel id');
        }

        return $this->filePathsById[$id];
    }

    public function getIterator()
    {
        return new ArrayIterator($this->filePaths);
    }
}
