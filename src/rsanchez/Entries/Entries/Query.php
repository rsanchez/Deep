<?php

namespace rsanchez\Entries\Entries;

use rsanchez\Entries\Entries;
use rsanchez\Entries\Entries\FilterInterface;
use rsanchez\Entries\Channels;
use rsanchez\Entries\DbInterface;

class Query {
  protected $result;
  protected $filter;
  protected $channels;
  protected $db;
  protected $dynamicParameters;

  public function __construct(DbInterface $db, Channels $channels, $dynamicParameters = array())
  {
    $this->db = $db;
    $this->channels = $channels;
    $this->dynamicParameters = $dynamicParameters;
  }

  public function requireTable($which)
  {
    static $joined = array();

    $tables = array(
      'members' => 'members.member_id = channel_titles.author_id',
      'channel_data' => 'channel_data.entry_id = channel_titles.entry_id',
      'channels' => 'channels.channel_id = channel_titles.channel_id',
    );
    
    if (in_array($which, $joined) || ! isset($tables[$which]))
    {
      return;
    }
    
    $this->db->join($which, $tables[$which]);
    
    $joined[] = $which;
  }

  public function get()
  {
    return $this->db->get();
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
      /**
       * Start a-queryin'
       **/
      $this->db->select('channel_titles.*');
      $this->db->from('channel_titles');
      
      /*
      $sql = $this->db->_compile_select();
      exit($sql);
      $query = $this->db->query($sql);
      */
      
      $query = $this->db->get();

      $this->result = $query->result();

      $query->free_result();
    }

    return $this;
  }

  public function authorId(array $value)
  {
    $this->db->where_in('channel_titles.author_id', $value);
  }

  public function catLimit()
  {
    
  }

  public function category()
  {
    
  }

  public function categoryGroup()
  {
    
  }

  public function channelId(array $value)
  {
    $this->db->where_in('channel_titles.channel_id', $value);
  }

  public function disable(array $value)
  {
    if ( ! in_array('custom_fields', $value))
    {
      $this->requireTable('channel_data');

      $this->db->select('channel_data.*');
    }
    
    if ( ! in_array('member_data', $value))
    {
      $this->requireTable('members');
    }
  }

  public function displayBy()
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

  public function dynamicParameters()
  {
    if ( ! $this->dynamicParameters)
    {
      return;
    }
    
    $validParameters = array(
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
    
    foreach ($validParameters as $key)
    {
      if (array_key_exists($key, $this->dynamicParameters))
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

  public function dynamicStart()
  {
    
  }

  public function entryId(array $entryIds)
  {
    $this->db->where_in('channel_titles.entry_id', $entryIds);
  }

  public function entryIdFrom($entryId)
  {
    $this->db->where('channel_titles.entry_id >=', $entryId);
  }

  public function entryIdTo($entryId)
  {
    $this->db->where('channel_titles.entry_id <=', $entryId);
  }

  public function fixedOrder(array $entryIds, $sort = 'ASC')
  {
    $this->db->where_in('channel_titles.entry_id', $entryIds);
      
    $this->db->order_by('FIELD('.implode(', ', $entryIds).')', $sort, FALSE);
  }

  public function groupId(array $groupIds)
  {
    $this->requireTable('members');
  
    $this->db->where_in('members.group_id', $groupIds);
  }

  public function limit($limit)
  {
    $this->db->limit($limit);
  }

  public function monthLimit()
  {
    
  }

  public function offset($offset)
  {
    $this->db->offset($offset);
  }

  public function orderby(array $orderby)
  {
    foreach ($orderby as $order_by)
    {
      $this->db->order_by($order_by);
    }
  }

  public function paginate()
  {
    
  }

  public function paginateBase()
  {
    
  }

  public function paginateType()
  {
    
  }

  public function relatedCategoriesMode()
  {
    
  }

  public function relaxedCategories()
  {
    
  }

  public function requireEntry()
  {
    
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

  public function showCurrentWeek()
  {
    
  }

  public function showExpired()
  {
    if ($this->filter->show_expired !== TRUE)
    {
      $this->db->where("(`".$this->db->dbprefix('channel_titles')."`.`expiration_date` = '' OR  `".$this->db->dbprefix('channel_titles')."`.`expiration_date` > NOW())", NULL, FALSE);
    }
  }

  public function showFutureEntries()
  {
    if ($this->filter->show_future_entries !== TRUE)
    {
      $this->db->where('channel_titles.entry_date <=', time());
    }
  }

  public function showPages()
  {
    
  }

  public function sort()
  {
    
  }

  public function startDay()
  {
    
  }

  public function startOn()
  {
    if ($this->filter->start_on) {
      $this->db->where('channel_titles.entry_date >=', $this->filter->start_on);
    }
  }

  public function status()
  {
    $this->db->where_in('channel_titles.status', $this->filter->status);
  }

  public function stopBefore()
  {
    if ($this->filter->stop_before) {
      $this->db->where('channel_titles.entry_date <', $this->filter->stop_before);
    }
  }

  public function sticky($sticky)
  {
    if ($sticky)
    {
        //@TODO make orderby property
      array_unshift($this->filter->orderby, 'channel_titles.sticky desc');
    }
  }

  public function trackViews()
  {
    
  }

  public function uncategorizedEntries($uncategorizedEntries)
  {
    
  }

  public function urlTitle(array $urlTitle)
  {
    $this->db->where_in('channel_titles.url_title', $urlTitle);
  }

  public function username($username)
  {
    $this->requireTable('members');
    
    $this->db->where('members.username', $username);
  }

  public function weekSort()
  {
    
  }

  public function year($year)
  {
    $this->db->where('channel_titles.year', $year);
  }

  public function month($month)
  {
    $this->db->where('channel_titles.month', $month);
  }

  public function day($day)
  {
    $this->db->where('channel_titles.day', $day);
  }
}