<?php

namespace rsanchez\Entries;

use \rsanchez\Entries\Channels;
use \rsanchez\Entries\Db;
use \rsanchez\Entries\Channel\Query;
use \rsanchez\Entries\Channel\FieldGroup;
use \rsanchez\Entries\EE;

class Factory {
	public static function ee() {
		static $ee;

		if (is_null($ee)) {
			$ee = new EE();
		}

		return $ee;
	}

	public static function entries() {
    static $channels;

    if (is_null($channels)) {
      $channels = self::channels(self::db());
    }

    $db = self::db();

    return new Entries($db, $channels);
	}
}