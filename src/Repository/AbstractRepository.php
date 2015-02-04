<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use Illuminate\Database\Eloquent\Model;

/**
 * Repository of all Sites
 */
abstract class AbstractRepository implements RepositoryInterface
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
     * {@inheritdoc}
     */
    public function getCollection()
    {
        $this->loadCollection();

        return $this->collection;
    }

    /**
     * Defer loading of Collection until needed
     * @return bool whether or not the collection needed loading
     */
    protected function loadCollection()
    {
        if (is_null($this->collection)) {
            $this->collection = $this->model->all();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getCollection()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->model;
    }
}
