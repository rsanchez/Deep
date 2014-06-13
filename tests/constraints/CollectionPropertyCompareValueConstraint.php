<?php

class CollectionPropertyCompareValueConstraint extends PHPUnit_Framework_Constraint
{
    /**
     * @var mixed
     */
    protected $expected;

    protected $validOperators = [
        '==',
        '===',
        '!=',
        '<>',
        '!==',
        '<',
        '>',
        '<=',
        '>=',
    ];

    public function __construct($expected, $property, $comparisonOperator)
    {
        parent::__construct();

        $this->expected = $expected;
        $this->property = $property;
        $this->comparisonOperator = $comparisonOperator;

        if (! in_array($comparisonOperator, $this->validOperators)) {
            throw new Exception('Invalid operators: '.$comparisonOperator);
        }
    }

    protected function compare($value)
    {
        switch ($this->comparisonOperator)
        {
            case '==':
                return $value == $this->expected;
            case '===':
                return $value === $this->expected;
            case '!=':
                return $value != $this->expected;
            case '<>':
                return $value <> $this->expected;
            case '!==':
                return $value !== $this->expected;
            case '<':
                return $value < $this->expected;
            case '>':
                return $value > $this->expected;
            case '<=':
                return $value <= $this->expected;
            case '>=':
                return $value >= $this->expected;
        }
    }

    public function matches($input)
    {
        foreach ($input as $row) {
            if ($this->compare($row->{$this->property})) {
                return false;
            }
        }

        return true;
    }

    public function toString()
    {
        return sprintf('%s does not have one of the values %s', $this->property, json_encode($this->expected));
    }
}
