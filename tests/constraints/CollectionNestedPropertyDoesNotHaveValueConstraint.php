<?php

class CollectionNestedPropertyDoesNotHaveValueConstraint extends PHPUnit_Framework_Constraint
{
    /**
     * @var mixed
     */
    protected $expected;

    public function __construct($expected, $mainProperty, $secondaryProperty)
    {
        parent::__construct();

        $this->expected = is_array($expected) ? $expected : [$expected];
        $this->mainProperty = $mainProperty;
        $this->secondaryProperty = $secondaryProperty;
    }

    public function matches($input)
    {
        foreach ($input as $row) {
            if (in_array($row->{$this->mainProperty}->{$this->secondaryProperty}, $this->expected)) {
                return false;
            }
        }

        return true;
    }

    public function toString()
    {
        return sprintf('%s.%s does not have the value %s', $this->mainProperty, $this->secondaryProperty, json_encode($this->expected));
    }
}
