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

    public function __construct($expected, $property, $comparisonOperator = '===')
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
        if (is_object($value)) {
            $value = (string) $value;
        }

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
        if ($input->isEmpty()) {
            return false;
        }

        foreach ($input as $row) {
            if (! $this->compare($row->{$this->property})) {
                return false;
            }
        }

        return true;
    }

    public function toString()
    {
        return sprintf('%s is not %s than %s', $this->property, $this->comparisonOperator, json_encode($this->expected));
    }
}
