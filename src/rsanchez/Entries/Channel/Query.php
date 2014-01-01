<?php

namespace rsanchez\Entries\Channel;

use rsanchez\Entries\DbInterface;

class Query {
	protected $db;

	public function __construct(DbInterface $db)
	{
		$this->db = $db;
	}

	public function result()
	{
		$channels = array();

		$fields = array();

		$this->db->join('fieldtypes', 'fieldtypes.name = channel_fields.field_type');

		$query = $this->db->get('channel_fields');

		foreach ($query->result() as $row)
		{
			if ( ! isset($fields[$row->group_id]))
			{
				$fields[$row->group_id] = array();
			}

			if ($row->field_settings)
			{
				$row->field_settings = unserialize(base64_decode($row->field_settings));
			}
			else
			{
				$row->field_settings = array();
			}

			//@TODO global field settings

			$fields[$row->group_id][] = $row;
		}

		$query->free_result();

		$query = $this->db->get('exp_channels');

		$result = $query->result();

		foreach ($result as $row)
		{
			if ($row->field_group && isset($fields[$row->field_group]))
			{
				$row->fields =& $fields[$row->field_group];
			}
			else
			{
				$row->fields = array();
			}

			$channels[$row->channel_id] =& $row;
		}

		$query->free_result();

		return $channels;
	}
}