<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Allows you to get Relationship siblings
 */
class BelongsToManySiblings extends BelongsToMany
{
    /**
     * @var string name of the parent id column
     */
    protected $parentKey;

    /**
     * @var string name of the order column
     */
    protected $orderColumn;

    /**
     * Create a new has many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Database\Eloquent\Model   $parent
     * @param  string                                $table
     * @param  string                                $foreignKey
     * @param  string                                $otherKey
     * @param  string                                $relationName
     * @param  string                                $parentKey
     * @param  string                                $orderColumn
     * @return void
     */
    public function __construct(Builder $query, Model $parent, $table, $foreignKey, $otherKey, $relationName, $parentKey, $orderColumn)
    {
        $this->parentKey = $parentKey;
        $this->orderColumn = $orderColumn;

        parent::__construct($query, $parent, $table, $foreignKey, $otherKey, $relationName);
    }
    /**
     * {@inheritdoc}
     */
    public function get($columns = array('*'))
    {
        $connection = $this->query->getQuery()->getConnection();
        $tablePrefix = $connection->getTablePrefix();

        $this->query->join($connection->raw("`{$tablePrefix}{$this->table}` AS `{$tablePrefix}{$this->table}_2`"), "{$this->table}_2.{$this->parentKey}", '=', "{$this->table}.{$this->parentKey}")
            ->addSelect("{$this->table}_2.*")
            ->orderBy("{$this->table}.{$this->orderColumn}", 'asc')
            ->groupBy("{$this->table}_2.{$this->foreignKey}")
            ->groupBy("{$this->parent->getTable()}.entry_id");

        return parent::get($columns);
    }

    /**
    * {@inheritdoc}
    */
    protected function buildDictionary(Collection $results)
    {
        $foreign = $this->foreignKey;

        $dictionary = array();

        foreach ($results as $result) {
            if ($result->$foreign !== $result->getKey()) {
                $dictionary[$result->$foreign][] = $result;
            }
        }

        return $dictionary;
    }
}
