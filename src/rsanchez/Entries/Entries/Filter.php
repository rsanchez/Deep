<?php

namespace rsanchez\Entries\Entries;

class Filter implements FilterInterface {

	public $author_id = array();
	public $cache = FALSE;
	public $refresh = 60;
	public $cat_limit;
	public $category = array();
	public $category_group = array();
	public $channel = array();
	public $channel_id = array();
	public $disable = array();
	public $display_by;
	public $dynamic = TRUE;
	public $dynamic_parameters;
	public $dynamic_start;
	public $entry_id = array();
	public $not_entry_id = array();
	public $entry_id_from;
	public $entry_id_to;
	public $fixed_order = array();
	public $group_id = array();
	public $not_group_id = array();
	public $limit;
	public $month_limit;
	public $offset;
	public $orderby = array('entry_date');
	public $paginate;
	public $paginate_base;
	public $paginate_type;
	public $related_categories_mode;
	public $relaxed_categories;
	public $require_entry;
	public $search;
	public $show_current_week = FALSE;
	public $show_expired = FALSE;
	public $show_future_entries = FALSE;
	public $show_pages;
	public $sort = 'desc';
	public $start_day;
	public $start_on;
	public $status = 'open';
	public $stop_before;
	public $sticky = FALSE;
	public $track_views;
	public $uncategorized_entries = TRUE;
	public $url_title;
	public $username;
	public $week_sort = 'desc';
	public $year;
	public $month;
	public $day;

	public function setInteger($name, $value)
	{
		if (is_int($value))
		{
			$this->$name = $value;
		}
		else if (ctype_digit($value))
		{
			$this->$name = intval($value);
		}

		return $this;
	}

	public function setBool($name, $value)
	{
		$this->$name = (bool) $value;

		return $this;
	}

	public function setString($name, $value)
	{
		$this->$name = strval($value);

		return $this;
	}

	public function setArray($name, $value)
	{
		if ( ! is_array($value))
		{
			if (func_num_args() > 2)
			{
				$this->$name = array_slice(func_get_args(), 1);
			}
			else
			{
				$this->$name = explode('|', strval($value));
			}
		}
		else
		{
			$this->$name = array();
			
			foreach ($value as $v)
			{
				$this->{$name}[] = strval($v);
			}
		}

		return $this;
	}

	public function setArrayInteger($name, $value)
	{
		if ( ! is_array($value))
		{
			if (func_num_args() > 2)
			{
				$value = array_slice(func_get_args(), 1);
			}
			else
			{
				$value = explode('|', $value);
			}
		}

		$this->$name = array();
		
		foreach ($value as $v)
		{
			if (ctype_digit($v))
			{
				$this->{$name}[] = intval($v);
			}
		}

		return $this;
	}

	public function setDate($name, $value)
	{
		if (ctype_digit($value))
		{
			$this->$name = intval($value);
		}
		else if (($value = strtotime($value)) !== FALSE)
		{
			$this->$name = $value;
		}

		return $this;
	}

	public function setRegex($name, $value, $pattern)
	{
		if (preg_match($pattern, $value))
		{
			$this->$name = $value;
		}

		return $this;
	}

	
}