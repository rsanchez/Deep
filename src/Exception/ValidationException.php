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

        $messages = [];

        foreach ($this->errors->getMessages() as $error) {
            $messages[] = implode(PHP_EOL, $error);
        }

        $this->message = implode(PHP_EOL, $messages);
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
