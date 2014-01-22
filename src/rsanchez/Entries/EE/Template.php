<?php

namespace rsanchez\Entries\EE;

use \EE_Template;

class Template extends EE_Template {

	public function parse_variables($tagdata, $data) {
		return $data;
	}

	/*
	public function parse_variables_row($tagdata, $data) {
		return $data;
	}
	*/
}