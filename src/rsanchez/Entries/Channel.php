<?php

namespace rsanchez\Entries;

use rsanchez\Entries\Channel\Query;
use rsanchez\Entries\Channel\Field;

class Channel {
	protected $meta;

	public $fields = array();

	public function __construct($id) {
		$meta = Query::channel($id);

		if ( ! is_null($meta))
		{
			$this->meta = $meta;

			$this->fields = array();

			foreach (Query::fields($this->meta['field_group']) as $row) {
				$this->fields[] = Field::create($row->field_id, $row->field_name, $row->field_type, $row);
			}
		}
	}
}