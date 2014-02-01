<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use rsanchez\Deep\FilePath\Repository as FilePathRepository;
use stdClass;

class File extends Fieldtype
{
    public function __construct(stdClass $row, FilePathRepository $filePathRepository)
    {
        parent::__construct($row);

        $this->filePathRepository = $filePathRepository;
    }

    public function __invoke($value)
    {
        if (! $value) {
            return '';
        }

        if (preg_match('/^{filedir_(\d+)}/', $value, $match)) {
            try {
                $filePath = $this->filePathRepository->find($match[1]);

                return str_replace($match[0], $filePath->url, $value);
            } catch (\Exception $e) {
                //$e->getMessage();
                return '';
            }
        }

        return $value;
    }
}
