<?php

namespace rsanchez\Deep\Entity\Field;

use rsanchez\Deep\Db\DbInterface;
use rsanchez\Deep\Channel\Channel;
use rsanchez\Deep\FilePath\Repository as FilePathRepository;
use rsanchez\Deep\Entity\Field\Field;
use rsanchez\Deep\Entity\Collection as EntityCollection;
use rsanchez\Deep\Property\AbstractProperty;
use rsanchez\Deep\Entity\Entity;

class File extends Field
{
    public function __construct(
        $value,
        AbstractProperty $property,
        EntityCollection $entries,
        $entity = null,
        FilePathRepository $filePathRepository
    ) {
        parent::__construct($value, $property, $entries, $entity);

        $this->filePathRepository = $filePathRepository;
    }

    public function __toString()
    {
        if (! $this->value) {
            return '';
        }

        $value = $this->value;

        if (preg_match('/^{filedir_(\d+)}/', $this->value, $match)) {
            try {
                $filePath = $this->filePathRepository->find($match[1]);

                return str_replace($match[0], $filePath->url, $this->value);
            } catch (\Exception $e) {
                //$e->getMessage();
                return '';
            }
        }

        return $this->value;
    }
}
