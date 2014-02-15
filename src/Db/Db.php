<?php

namespace rsanchez\Deep\Db;

use Illuminate\CodeIgniter\CodeIgniterConnectionResolver;
use PDO;

class Db
{
    protected $connection;

    public function __construct(CodeIgniterConnectionResolver $resolver)
    {
        $this->connection = $resolver->connection();
        $this->connection->setFetchMode(PDO::FETCH_OBJ);
    }

    public function escape($string)
    {
        if (is_callable('PDO::quote')) {
            return PDO::quote($string);
        }

        if (function_exists('mysqli_escape_string')) {
            return mysqli_escape_string($string);
        }

        throw new \Exception('You must have mysqli or PDO installed.');
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->connection, $name), $args);
    }
}
