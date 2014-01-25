<?php

namespace rsanchez\Entries;

use rsanchez\Entries\DbInterface;
use \CI_DB_mysql_driver;

class Db extends CI_DB_mysql_driver implements DbInterface
{
}
