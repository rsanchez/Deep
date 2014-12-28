<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Repository of all Sites
 */
abstract class AbstractDeferredRepository implements RepositoryInterface
{
    /**
     * Repository Model
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Collection of all records
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $collection;

    /**
     * Constructor
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Defer loading of Collection until needed
     * @return void
     */
    protected function boot()
    {
        if (! is_null($this->collection)) {
            return;
        }

        $this->collection = $this->model->all();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $this->boot();

        return $this->collection->find($id);
    }

    /**
     * Get the repository's model instance
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }
}
