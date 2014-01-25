<?php

namespace rsanchez\Entries;

interface DbInterface
{
    public function select($select, $escape);

    public function select_max($select, $alias);

    public function select_min($select, $alias);

    public function select_avg($select, $alias);

    public function select_sum($select, $alias);

    public function distinct($val);

    public function from($from);

    public function join($table, $cond, $type);

    public function where($key, $value, $escape);

    public function or_where($key, $value, $escape);

    public function where_in($key, $values);

    public function or_where_in($key, $values);

    public function where_not_in($key, $values);

    public function or_where_not_in($key, $values);

    public function like($field, $match, $side);

    public function not_like($field, $match, $side);

    public function or_like($field, $match, $side);

    public function or_not_like($field, $match, $side);

    public function group_by($by);

    public function having($key, $value, $escape);

    public function or_having($key, $value, $escape);

    public function order_by($orderby, $direction, $escape);

    public function limit($value, $offset);

    public function offset($offset);

    #public function set($key, $value, $escape);

    public function get($table, $limit, $offset);

    public function count_all_results($table);

    public function get_where($table, $where, $limit, $offset);

    #public function insert_batch($table, $set);

    #public function set_insert_batch($key, $value, $escape);

    #public function insert($table, $set);

    #public function replace($table, $set);

    #public function update($table, $set, $where, $limit);

    #public function update_batch($table, $set, $index);

    #public function set_update_batch($key, $index, $escape);

    #public function empty_table($table);

    #public function truncate($table);

    #public function delete($table, $where, $limit, $reset_data);

    #public function dbprefix($table);

    #public function start_cache();

    #public function stop_cache();

    #public function flush_cache();
}
