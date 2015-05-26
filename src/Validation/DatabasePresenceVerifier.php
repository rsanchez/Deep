<?php

namespace rsanchez\Deep\Validation;

use Illuminate\Validation\DatabasePresenceVerifier as IlluminateDatabasePresenceVerifier;

class DatabasePresenceVerifier extends IlluminateDatabasePresenceVerifier
{
    protected $caching = true;

    protected $cache;

    public function enableCaching()
    {
        $this->caching = true;
    }

    public function disableCaching()
    {
        $this->caching = false;
    }

    protected function cacheExists($key)
    {
        return isset($this->cache[$key]);
    }

    protected function cacheGet($key)
    {
        return $this->cache[$key];
    }

    protected function cacheSet($key, $value)
    {
        $this->cache[$key] = $value;
    }

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  string  $value
     * @param  int     $excludeId
     * @param  string  $idColumn
     * @param  array   $extra
     * @return int
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $argHash = md5(serialize(func_get_args()));

        if ($this->caching && $this->cacheExists($argHash)) {
            return $this->cacheGet($argHash);
        }

        $count = parent::getCount($collection, $column, $value, $excludeId, $idColumn, $extra);

        $this->cacheSet($argHash, $count);

        return $count;
    }

    /**
     * Count the number of objects in a collection with the given values.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  array   $values
     * @param  array   $extra
     * @return int
     */
    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        $argHash = md5(serialize(func_get_args()));

        if ($this->caching && $this->cacheExists($argHash)) {
            return $this->cacheGet($argHash);
        }

        $count = parent::getMultiCount($collection, $column, $values, $extra);

        $this->cacheSet($argHash, $count);

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    protected function addWhere($query, $key, $extraValue)
    {
        if (is_array($extraValue)) {
            $query->whereIn($key, $extraValue);
        } else {
            parent::addWhere($query, $key, $extraValue);
        }
    }
}
