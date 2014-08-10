<?php

class CollectionNestedCollectionPropertyHasOneValueConstraint extends PHPUnit_Framework_Constraint
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
            $found = false;

            foreach ($row->{$this->mainProperty} as $secondaryRow) {
                if (in_array($secondaryRow->{$this->secondaryProperty}, $this->expected)) {
                    $found = true;
                }
            }

            if (! $found) {
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
