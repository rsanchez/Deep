<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * Model which can optionally join the specified table(s)
 */
trait JoinableTrait
{
    /**
     * Find a model by its primary key.
     *
     * @param  mixed                                          $id
     * @param  array                                          $columns
     * @return \Illuminate\Database\Eloquent\Model|Collection
     */
    public static function find($id, $columns = ['*'])
    {
        $instance = new static();

        if (is_array($id) && empty($id)) {
            return $instance->newCollection();
        }

        $query = $instance->newQuery();

        $key = $instance->table.'.'.$instance->primaryKey;

        if (is_array($id)) {
            return $query->whereIn($key, $id)
                ->get($columns);
        }

        return $query->where($key, '=', $id)
            ->first($columns);
    }

    /**
     * Get a new query builder that doesn't have any global scopes.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newQueryWithoutScopes()
    {
        $query = parent::newQueryWithoutScopes();

        // no joins for saving
        $query->getQuery()->joins = [];

        return $query;
    }

    /**
     * Set the global eloquent scope
     * @return void
     */
    protected static function bootJoinableTrait()
    {
        $tables = static::defaultJoinTables();

        if ($tables) {
            static::addGlobalScope(new JoinableScope($tables));
        }
    }

    /**
     * Set which tables should be joined automatically
     * @return array
     */
    public static function defaultJoinTables()
    {
        return [];
    }

    /**
     * Return a structured array of joinable tables
     * ex.
     *     'members' => ['members.member_id', 'channel_titles.author_id'],
     *
     * @return array
     */
    protected static function joinTables()
    {
        return [];
    }

    /**
     * Join the required table, once
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  string                                $table   table name
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function requireTable(Builder $builder, $table)
    {
        $tables = static::joinTables();

        if (! isset($tables[$table])) {
            return $builder;
        }

        $callback = $tables[$table];

        $query = $builder->getQuery();

        // don't join twice
        if ($query->joins) {
            foreach ($query->joins as $joinClause) {
                if ($joinClause->table === $table) {
                    return $builder;
                }
            }
        }

        $callback($builder);

        return $builder;
    }
}
