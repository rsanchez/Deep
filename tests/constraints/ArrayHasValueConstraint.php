<?php

class ArrayHasValueConstraint extends PHPUnit_Framework_Constraint
{
    /**
     * @var mixed
     */
    protected $expected;

    public function __construct($expected)
    {
        parent::__construct();

        $this->expected = $expected;
    }

    public function matches($input)
    {
        return in_array($this->expected, $input);
    }

    public function toString()
    {
        return 'has the value '.$this->expected;
    }
}
