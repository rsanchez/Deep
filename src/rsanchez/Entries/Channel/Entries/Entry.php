<?php

namespace rsanchez\Entries\Channel\Entries;

use \rsanchez\Entries\Channel;

class Entry {

  public function __construct(Channel $channel, $data) {
    $this->channel = $channel;
    foreach ($data as $key => $value) {
      $this->$key = $value;
    }
  }
}