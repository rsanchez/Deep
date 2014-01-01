<?php

namespace rsanchez\Entries;

use rsanchez\Entries\DbInterface;
use rsanchez\Entries\Db;
use \CI_DB_mysql_driver;

class Db extends CI_DB_mysql_driver implements DbInterface {

  public $rdriver = null;

  public function load_rdriver()
  {
    if ( ! $this->rdriver) {
      return parent::load_rdriver();
    }

    $rdriver = $this->rdriver;

    $this->rdriver = null;

    return $rdriver;
  }

}