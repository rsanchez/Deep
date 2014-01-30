<?php

namespace rsanchez\Entries\Db;

use rsanchez\Entries\Db\DbInterface;
use \CI_DB_mysql_driver;

class Db extends CI_DB_mysql_driver implements DbInterface
{
}
