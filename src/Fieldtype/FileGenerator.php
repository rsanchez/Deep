<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\File;
use rsanchez\Deep\FilePath\Repository as FilePathRepository;
use stdClass;

class FileGenerator
{
    private $filePathRepository;

    public function __construct(FilePathRepository $filePathRepository)
    {
        $this->filePathRepository = $filePathRepository;
    }

    public function __invoke(stdClass $row)
    {
        return new File($row, $this->filePathRepository);
    }
}
