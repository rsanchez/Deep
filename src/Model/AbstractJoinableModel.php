<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Abstract Model which can optionally join the specified table(s)
 */
abstract class AbstractJoinableModel extends Model
{
    /**
     * Return a structured array of joinable tables
     * ex.
     *     'members' => array('members.member_id', 'channel_titles.author_id'),
     *
     * @return array
     */
    protected static function joinTables()
    {
        return array();
    }

    /**
     * Join the required table, once
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  string                                $table  table name
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    protected function requireTable(Builder $builder, $table)
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
