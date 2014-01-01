<?php

namespace rsanchez\Entries;

use \rsanchez\Entries\Factory;
use \rsanchez\Entries\Entries\Filter;
use \rsanchez\Entries\Entries\Entry;
use \rsanchez\Entries\Entries\Query as EntriesQuery;
use \rsanchez\Entries\Entries\Collection;
use \rsanchez\Entries\Db;

class Entries extends Collection {

  private $filter;

  private $db;

  private $query;

  private static $channels;

  public function __construct(array $entries = array())
  {
    parent::__construct($entries);

    $this->filter = new Filter;
    $this->db = Factory::db();
    $this->channels = Factory::channels();
  }

  public function get()
  {
    if (is_null($this->query)) {
      $this->query = new EntriesQuery($this->db, $this->filter, $this->channels);

      $this->entries = array();

      foreach ($this->query->result() as $row) {
        $this->entries[] = new Entry($this->channels->find($row->channel_id), $row);
      }
    }

    return $this;
  }

  public function valid()
  {
    $this->get();

    return parent::valid();
  }

  public function channel($value)
  {
    $this->filter->setArray('channel', $value);

    return $this;
  }

  public function channel_id($value)
  {
    $this->filter->setArrayInteger('channel_id', $value);

    return $this;
  }

  public function author_id($value)
  {
    $this->filter->setArrayInteger('author_id', $value);

    return $this;
  }

  public function cat_limit($value) {
    $this->filter->setInteger('cat_limit', $value);

    return $this;
  }

  public function category($value) {
    $this->filter->setArrayInteger('category', $value);

    return $this;
  }

  public function category_group($value) {
    $this->filter->setArrayInteger('category_group', $value);

    return $this;
  }

  public function display_by($value) {
    $this->filter->setRegex('display_by', $value, '/^month|day|number$/i');

    return $this;
  }

  public function dynamic($value) {
    $this->filter->setBool('dynamic', $value);

    return $this;
  }

  public function sticky($value) {
    $this->filter->setBool('sticky', $value);

    return $this;
  }

  public function entry_id($value) {
    $this->filter->setArrayInteger('entry_id', $value);

    return $this;
  }

  public function not_entry_id($value) {
    $this->filter->setArrayInteger('not_entry_id', $value);

    return $this;
  }

  public function entry_id_from($value) {
    $this->filter->setInteger('entry_id_from', $value);

    return $this;
  }

  public function entry_id_to($value) {
    $this->filter->setInteger('entry_id_to', $value);

    return $this;
  }

  public function fixed_order($value) {
    $this->filter->setArrayInteger('fixed_order', $value);

    return $this;
  }

  public function group_id($value) {
    $this->filter->setArrayInteger('group_id', $value);

    return $this;
  }

  public function not_group_id($value) {
    $this->filter->setArrayInteger('not_group_id', $value);

    return $this;
  }

  public function limit($value) {
    $this->filter->setInteger('limit', $value);

    return $this;
  }

  public function month_limit($value) {
    $this->filter->setInteger('month_limit', $value);

    return $this;
  }

  public function offset($value) {
    $this->filter->setInteger('offset', $value);

    return $this;
  }

  public function orderby($value) {
    $this->filter->setString('orderby', $value);

    return $this;
  }

  public function require_entry($value) {
    $this->filter->setBool('require_entry', $value);

    return $this;
  }

  public function search($value) {
    $this->filter->setRegex('search', $value);

    return $this;
  }

  public function show_current_week($value) {
    $this->filter->setBool('show_current_week', $value);

    return $this;

  }

  public function show_expired($value) {
    $this->filter->setBool('show_expired', $value);

    return $this;
  }

  public function show_future_entries($value) {
    $this->filter->setBool('show_future_entries', $value);

    return $this;
  }

  public function sort($value) {
    $this->filter->setRegex('sort', $value, '/^asc|desc$/i');

    return $this;
  }

  public function start_day($value) {
    $this->filter->setRegex('start_day', $value, '/^Monday|Sunday$/i');

    return $this;
  }

  public function start_on($value) {
    $this->filter->setDate('start_on', $value);

    return $this;
  }

  public function status($value) {
    $this->filter->setString('status', $value);

    return $this;
  }

  public function stop_before($value) {
    $this->filter->setDate('stop_before', $value);

    return $this;
  }

  public function uncategorized_entries($value) {
    $this->filter->setBool('uncategorized_entries', $value);

    return $this;
  }

  public function url_title($value) {
    $this->filter->setString('url_title', $value);

    return $this;
  }

  public function username($value) {
    $this->filter->setString('username', $value);

    return $this;
  }

  public function week_sort($value) {
    $this->filter->setRegex('week_sort', $value, '/^asc|desc$/i');

    return $this;
  }

  public function year($value) {
    $this->filter->setInteger('year', $value);

    return $this;
  }

  public function month($value) {
    $this->filter->setInteger('month', $value);

    return $this;
  }

  public function day($value) {
    $this->filter->setInteger('day', $value);

    return $this;
  }
}

