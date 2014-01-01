<?php

namespace rsanchez\Entries\Channel;

class Field {
	public static $classMap = array(
		'matrix' => 'MatrixField',
		'playa' => 'PlayaField',
		'relationships' => 'RelationshipsField',
		'grid' => 'GridField',
	);

	public static function create($field_id, $field_name, $field_type, $params) {

		$class = 'rsanchez\Entries\Channel\\';

		$class .= isset(self::$classMap[$field_type]) ? self::$classMap[$field_type] : 'Field';

		return new $class($field_id, $field_name, $params);
	}

	public function __construct($field_id, $field_name, $params) {
		
	}
}