<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \Illuminate\Database\Eloquent\Model
 */
abstract class AbstractModelCollection extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->addModel($item);
    }

    /**
     * {@inheritdoc}
     */
    public function add($item)
    {
        $this->addModel($item);

        return $this;
    }

    /**
     * Add a Model to this collection
     * @param  \Illuminate\Database\Eloquent\Model $item
     * @return void
     */
    abstract public function addModel(Model $item);
}
