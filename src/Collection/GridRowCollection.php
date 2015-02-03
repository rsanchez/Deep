<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\GridRow;
use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Model\PropertyInterface;

/**
 * Collection of \rsanchez\Deep\Model\GridRow
 */
class GridRowCollection extends AbstractModelCollection implements FilterableInterface
{
    use FilterableTrait;

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
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addGridRow($item);
    }

    /**
     * Add a GridRow to this collection
     * @param  \rsanchez\Deep\Model\GridRow $item
     * @return void
     */
    public function addGridRow(GridRow $item)
    {
        $this->items[] = $item;
    }

    /**
     * Add a new GridRow to this collection
     * @param array $attributes
     * @return \rsanchez\Deep\Model\GridRow
     */
    public function addRow(array $attributes = [])
    {
        $property = $this->getProperty();

        if ($property) {
            $key = $property->getPrefix().'_id';//field_id

            $attributes = [$key => $property->getId()] + $attributes;
        }

        $row = new GridRow($attributes);

        $this->push($row);

        return $row;
    }
}
