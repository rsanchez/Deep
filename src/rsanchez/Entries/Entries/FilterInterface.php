<?php

namespace rsanchez\Entries\Entries;

interface FilterInterface {

	public function setInteger($name, $value);

	public function setBool($name, $value);

	public function setString($name, $value);

	public function setArray($name, $value);

	public function setArrayInteger($name, $value);

	public function setDate($name, $value);

	public function setRegex($name, $value, $pattern);
}