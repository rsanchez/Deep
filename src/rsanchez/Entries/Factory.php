<?php

namespace rsanchez\Entries;

use \rsanchez\Entries\Channels;
use \rsanchez\Entries\Db;
use \rsanchez\Entries\Channel\Query;

class Factory {
	public static function db() {
   return new Db(array(
			'dbdriver' => 'mysql',
			'conn_id' => ee()->db->conn_id,
			'database' => ee()->db->database,
			'dbprefix' => ee()->db->dbprefix,
		));
	}

	public static function channels() {
		static $channels;

		if (is_null($channels)) {
			$query = new Query(self::db());

			foreach ($query->result() as $row) {
				$channels[] = new Channel($row);
			}
		}

		return new Channels($channels);
	}
}