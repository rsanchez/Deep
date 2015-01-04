<?php

namespace rsanchez\Deep\Exception;

use Illuminate\Support\MessageBag;
use RuntimeException;

class ValidationException extends RuntimeException
{
    /**
     * @var Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Constructor
     *
     * @param \Illuminate\Support\MessageBag $errors
     */
    public function __construct (MessageBag $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Get errors associated with this exception
     * @return Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
