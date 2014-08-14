<?php

class ArrayHasOnlyValuesConstraint extends PHPUnit_Framework_Constraint
{
    /**
     * @var array
     */
    protected $expected;

    public function __construct(array $expected)
    {
        parent::__construct();

        $this->expected = $expected;
    }

    public function matches($input)
    {
        return ! array_diff($this->expected, $input) && ! array_diff($input, $this->expected);
    }

    public function toString()
    {
        return 'has the same values as '.json_encode($this->expected);
    }
}
