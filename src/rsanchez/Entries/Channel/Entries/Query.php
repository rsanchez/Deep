<?php

namespace rsanchez\Entries\Channel\Entries;

use rsanchez\Entries\Channel\Entries;
use rsanchez\Entries\Channel\Entries\Filter;
use rsanchez\Entries\Channels;
use rsanchez\Entries\Db;

class Query {
  protected $result;
  protected $filter;
  protected $channels;
  protected $db;

  protected $joined = array();

  public function __construct(Db $db, Filter $filter, Channels $channels, $dynamic_parameters = array())
  {
    $this->db = $db;
    $this->filter = $filter;
    $this->channels = $channels;
    $this->dynamic_parameters = $dynamic_parameters;
  }


  
  public function requireTable($which)
  {
    $tables = array(
      'members' => 'members.member_id = channel_titles.author_id',
      'channel_data' => 'channel_data.entry_id = channel_titles.entry_id',
      'channels' => 'channels.channel_id = channel_titles.channel_id',
    );
    
    if (in_array($which, $this->joined) || ! isset($tables[$which]))
    {
      return;
    }
    
    $this->db->join($which, $tables[$which]);
    
    $this->joined[] = $which;
  }

  public function result()
  {
    $this->execute();

    return $this->result;
  }

  public function execute()
  {
    if (is_null($this->result))
    {
      if ($this->filter->channel)
      {
        foreach ($this->filter->channel as $name) {
          try {
            $channel = $this->channels->find($name);

            $this->filter->channel_id[] = $channel->channel_id;

          } catch (Exception $e) {
            //@TODO
          }
        }
      }

      /**
       * Start a-queryin'
       **/
      $this->db->select('channel_titles.*');
      
      $order = array(
        //'channel',
        'channel_id',
        'dynamic_parameters',
        'disable',
        'author_id',
        //'cache',
        //'refresh',
        'cat_limit',
        'category',
        'category_group',
        'display_by',
        'dynamic',
        'sticky',
        'entry_id',
        //@TODO//'not_entry_id',
        'entry_id_from',
        'entry_id_to',
        'fixed_order',
        'group_id',
        //@TODO//'not_group_id',
        'limit',
        'month_limit',
        'offset',
        'orderby',
        'require_entry',
        'search',
        'show_current_week',
        'show_expired',
        'show_future_entries',
        'sort',
        'start_day',
        'start_on',
        'status',
        'stop_before',
        'uncategorized_entries',
        'url_title',
        'username',
        'week_sort',
        'year',
        'month',
        'day',
      );

      foreach ($order as $which)
      {
        if ( ! is_null($this->filter->$which))
        {
          $this->$which();
        }
      }
      
      /*
      $this->db->from('channel_titles');

      $sql = $this->db->_compile_select();
      exit($sql);
      $query = $this->db->query($sql);
      */
      
      $query = $this->db->get('channel_titles');

      $this->result = $query->result();

      $query->free_result();
    }

    return $this;
  }

  public function author_id()
  {
    if ($this->filter->author_id) {
      $author_id = array_map(function($value) {
        return $value === 'CURRENT_USER' ? ee()->session->userdata('member_id') : $value;
      }, $this->filter->author_id);

      $this->db->where_in('channel_titles.author_id', $author_id);
    }
  }

  public function cat_limit()
  {
    
  }

  public function category()
  {
    
  }

  public function category_group()
  {
    
  }

  public function channel_id()
  {
    if ($this->filter->channel_id) {
      $this->db->where_in('channel_titles.channel_id', $this->filter->channel_id);
    }
  }

  public function disable()
  {
    if ( ! in_array('custom_fields', $this->filter->disable))
    {
      $this->requireTable('channel_data');
      
      foreach ($this->filter->channel_id as $channel_id)
      {
        try {
          $channel = $this->channels->find($channel_id);

          foreach ($channel->fields as $field) {
            $this->db->select('channel_data.field_id_'.$field->field_id.' AS `'.$field->field_name.'`');
          }

        } catch (Exception $e) {
          //@TODO
        }
      }
    }
    
    if ( ! in_array('member_data', $this->filter->disable))
    {
      $this->requireTable('members');
    }
  }

  public function display_by()
  {
    
  }

  public function dynamic()
  {
    if ($this->filter->dynamic !== TRUE)
    {
      return;
    }
    
    //entry_id, url_title, month/year/day
  }

  public function dynamic_parameters()
  {
    if ( ! $this->dynamic_parameters)
    {
      return;
    }
    
    $valid_parameters = array(
      'author_id',
      'cat_limit',
      'category',
      'channel',
      'channel_id',
      'day',
      'display_by',
      'entry_id',
      'entry_id_from',
      'entry_id_to',
      'group_id',
      'limit',
      'month',
      'month_limit',
      'not_entry_id',
      'offset',
      'orderby',
      'show_expired',
      'show_future_entries',
      'sort',
      'start_on',
      'status',
      'sticky',
      'stop_before',
      'username',
      'year',
      'search',
      'exact_search',
    );
    
    foreach ($this->filter->dynamic_parameters as $key)
    {
      if (array_key_exists($key, $this->dynamic_parameters))
      {
        if (in_array($key, $valid_parameters))
        {
          $this->{$key}($this->dynamic_parameters[$key]);
        }
        
        if (strncmp($key, 'search:', 7) === 0)
        {
          $this->search(substr($key, 7), $this->dynamic_parameters[$key]);
        }
      }
    }
  }

  public function dynamic_start()
  {
    
  }

  public function entry_id()
  {
    if ($this->filter->entry_id) {
      $this->db->where_in('channel_titles.entry_id', $this->filter->entry_id);
    }
  }

  public function entry_id_from()
  {
    if ($this->filter->entry_id_from) {
      $this->db->where('channel_titles.entry_id >=', $this->filter->entry_id_from);
    }
  }

  public function entry_id_to()
  {
    if ($this->filter->entry_id_to) {
      $this->db->where('channel_titles.entry_id <=', $this->filter->entry_id_to);
    }
  }

  public function fixed_order()
  {
    if ($this->filter->fixed_order) {
      $this->db->where_in('channel_titles.entry_id', $this->filter->fixed_order);
      
      $this->db->order_by('FIELD('.implode(', ', $this->filter->fixed_order).')', $this->filter->sort, FALSE);
    }
  }

  public function group_id()
  {
    if ($this->filter->group_id) {
      $this->requireTable('members');
      
      $this->db->where_in('members.group_id', $this->filter->group_id);
    }
  }

  public function limit()
  {
    $this->db->limit($this->filter->limit);
  }

  public function month_limit()
  {
    
  }

  public function offset()
  {
    $this->db->offset($this->filter->offset);
  }

  public function orderby()
  {
    foreach ($this->filter->orderby as $order_by)
    {
      $this->db->order_by($order_by);
    }
  }

  public function paginate()
  {
    
  }

  public function paginate_base()
  {
    
  }

  public function paginate_type()
  {
    
  }

  public function related_categories_mode()
  {
    
  }

  public function relaxed_categories()
  {
    
  }

  public function require_entry()
  {
    
  }
  
  private function _get_field_id($field_name)
  {
    $fields = $this->_get_fields();
    
    foreach ($fields as $field)
    {
      if ($field->field_name === $field_name)
      {
        return $field->field_id;
      }
    }
    
    return FALSE;
  }
  
  private function _get_fields()
  {
    if (isset($this->fields))
    {
      return $this->fields;
    }
    
    $db = Entries::new_db();
    
    if ($this->field_groups)
    {
      $db->where_in('group_id', $this->field_groups);
    }
    
    $query = $db->get('channel_fields');
    
    $this->fields = array();
    
    foreach ($query->result() as $row)
    {
      $this->fields[$row->field_id] = $row;
    }
    
    $query->free_result();
    
    unset($db);
    
    return $this->fields;
  }

  public function search()
  {
    $this->requireTable('channel_data');
    
    foreach ($this->search as $field => $searches)
    {
      if ($field_id = $this->_get_field_id($field))
      {
        foreach ($searches as $search)
        {
          if ( ! is_array($search->value))
          {
            $search->value = explode('|', $search->value);
          }
          
          $queries = array();
          
          $glue = $search->and ? ' AND ' : ' OR ';
          
          foreach ($search->value as $value)
          {
            $query = "`".$this->db->dbprefix('channel_data')."`.`field_id_{$field_id}` ";
            
            $query .= $search->exact ? " = '".$this->db->escape_str($value).'"' : "LIKE '%".$this->db->escape_str($value)."%'";
            
            $queries[] = $query;
          }
          
          $this->db->where('('.implode($glue, $queries).')', NULL, FALSE);
        }
      }
    }
  }

  public function show_current_week()
  {
    
  }

  public function show_expired()
  {
    if ($this->filter->show_expired !== TRUE)
    {
      $this->db->where("(`".$this->db->dbprefix('channel_titles')."`.`expiration_date` = '' OR  `".$this->db->dbprefix('channel_titles')."`.`expiration_date` > NOW())", NULL, FALSE);
    }
  }

  public function show_future_entries()
  {
    if ($this->filter->show_future_entries !== TRUE)
    {
      $this->db->where('channel_titles.entry_date <=', time());
    }
  }

  public function show_pages()
  {
    
  }

  public function sort()
  {
    
  }

  public function start_day()
  {
    
  }

  public function start_on()
  {
    if ($this->filter->start_on) {
      $this->db->where('channel_titles.entry_date >=', $this->filter->start_on);
    }
  }

  public function status()
  {
    $this->db->where_in('channel_titles.status', $this->filter->status);
  }

  public function stop_before()
  {
    if ($this->filter->stop_before) {
      $this->db->where('channel_titles.entry_date <', $this->filter->stop_before);
    }
  }

  public function sticky()
  {
    if ($this->filter->sticky)
    {
      array_unshift($this->filter->orderby, 'channel_titles.sticky desc');
    }
  }

  public function track_views()
  {
    
  }

  public function uncategorized_entries()
  {
    
  }

  public function url_title()
  {
    if ($this->filter->url_title) {
      $this->db->where('channel_titles.url_title', $this->filter->url_title);
    }
  }

  public function username()
  {
    if ($this->filter->username) {
      $this->requireTable('members');
      
      $this->db->where('members.username', $this->filter->username);
    }
  }

  public function week_sort()
  {
    
  }

  public function year()
  {
    if ($this->filter->year) {
      $this->db->where('channel_titles.year', $this->filter->year);
    }
  }

  public function month()
  {
    if ($this->filter->month) {
      $this->db->where('channel_titles.month', $this->filter->month);
    }
  }

  public function day()
  {
    if ($this->filter->day) {
      $this->db->where('channel_titles.day', $this->filter->day);
    }
  }
}