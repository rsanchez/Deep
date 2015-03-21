<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\MatrixRow;
use rsanchez\Deep\Model\PropertyInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\MatrixRow
 */
class MatrixRowCollection extends AbstractModelCollection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * {@inheritdoc}
     */
    protected $modelClass = '\\rsanchez\\Deep\\Model\\MatrixRow';

    /**
     * @var \rsanchez\Deep\Model\PropertyInterface
     */
    protected $property;

    /**
     * Set the field for this collection of MatrixRows
     * @param \rsanchez\Deep\Model\PropertyInterface $property
     */
    public function setProperty(PropertyInterface $property)
    {
        $this->property = $property;
    }

    /**
     * Get the field for this collection of MatrixRows
     * @return \rsanchez\Deep\Model\PropertyInterface|null
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Add a new MatrixRow to this collection
     * @param array $attributes
     * @return \rsanchez\Deep\Model\MatrixRow
     */
    public function addRow(array $attributes = [])
    {
        $property = $this->getProperty();

        if ($property) {
            $key = $property->getPrefix().'_id';//field_id

            $attributes = [$key => $property->getId()] + $attributes;
        }

        $row = new MatrixRow($attributes);

        $this->push($row);

        return $row;
    }
}
