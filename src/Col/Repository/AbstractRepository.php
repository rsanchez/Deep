<?php

namespace rsanchez\Deep\Col\Repository;

use rsanchez\Deep\Col\Col;
use rsanchez\Deep\Col\Collection;
use rsanchez\Deep\Col\Storage\AbstractStorage;
use rsanchez\Deep\Col\Factory;
use rsanchez\Deep\Fieldtype\Fieldtype;

abstract class AbstractRepository extends Collection
{
    private $storage;
    private $factory;

    private $fieldIdsChecked = array();

    public function __construct(AbstractStorage $storage, Factory $factory)
    {
        $this->storage = $storage;
        $this->factory = $factory;
    }

    public function findByFieldIds(array $fieldIds)
    {
        $missingFieldIds = array_diff($fieldIds, $this->fieldIdsChecked);

        $this->fieldIdsChecked += $fieldIds;

        if ($missingFieldIds) {
            foreach ($this->storage->getByFieldIds($missingFieldIds) as $row) {
                $col = $this->factory->createCol($row);

                $this->attach($col);
            }
        }

        return $this->filterByFieldIds($fieldIds);
    }

    public function filterByFieldId($fieldId)
    {
        return $this->filterByFieldIds(array($fieldId));
    }

    public function filterByFieldIds(array $fieldIds)
    {
        $collectionClass = $this->filterClass ?: get_class($this);

        $collection = new $collectionClass();

        $properties = array_filter($this->properties, function ($col) use ($fieldIds) {
            return in_array($col->field_id, $fieldIds);
        });

        foreach ($properties as $property)
        {
            $collection->attach($property);
        }

        return $collection;
    }
}
