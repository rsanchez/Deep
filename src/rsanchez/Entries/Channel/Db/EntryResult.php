<?php

namespace rsanchez\Entries\Db;

use \CI_DB_mysql_result;

class EntryResult extends CI_DB_mysql_result {
  public function _fetch_object() {
    return @mysql_fetch_object($this->result_id, '\rsanchez\Entries\Channel\Entry');
  }
}