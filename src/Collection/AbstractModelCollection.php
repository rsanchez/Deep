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
use Illuminate\Support\Collection as SupportCollection;
use InvalidArgumentException;

/**
 * Collection of \Illuminate\Database\Eloquent\Model
 */
abstract class AbstractModelCollection extends Collection
{
    /**
     * @var model class required by collection
     */
    protected $modelClass;

    /**
     * {@inheritdoc}
     */
    public function __construct($items = [])
    {
        $items = is_null($items) ? [] : $this->getArrayableItems($items);

        foreach ((array) $items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $item
     */
    protected function validateModel($item)
    {
        if (! $item instanceof \Illuminate\Database\Eloquent\Model) {
            xdebug_break();
        }

        if ($this->modelClass && ! $item instanceof $this->modelClass) {
            throw new InvalidArgumentException("\$item must be an instance of {$this->modelClass}");
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $item
     */
    protected function prepareModel(Model $item)
    {
    }

    /**
     * Fetch a nested element of the collection.
     *
     * @param  string  $key
     * @return static
     */
    public function fetch($key)
    {
        return new SupportCollection(array_fetch($this->toArray(), $key));
    }

    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->validateModel($item);

        $this->prepareModel($item);

        return parent::push($item);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend($item)
    {
        $this->validateModel($item);

        $this->prepareModel($item);

        return parent::prepend($item);
    }

    /**
     * {@inheritdoc}
     */
    public function add($item)
    {
        $this->push($item);

        return $this;
    }
}
