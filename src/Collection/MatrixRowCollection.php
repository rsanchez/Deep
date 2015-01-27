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
        $this->addMatrixRow($item);
    }

    /**
     * Add a MatrixRow to this collection
     * @param  \rsanchez\Deep\Model\MatrixRow $item
     * @return void
     */
    public function addMatrixRow(MatrixRow $item)
    {
        $this->items[] = $item;
    }
}
