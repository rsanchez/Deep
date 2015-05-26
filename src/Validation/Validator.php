<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Validation;

use Illuminate\Validation\Validator as IlluminateValidator;

class Validator extends IlluminateValidator
{
    /**
     * Validate y or n
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @return bool
     */
    public function validateYesOrNo($attribute, $value, $parameters = [])
    {
        return $value === 'y' || $value === 'n';
    }

    /**
     * Validate in array where value itself is an array of values (i.e. a multiselect)
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateInArray($attribute, $value, $parameters = [])
    {
        return is_array($value) && ! array_diff($value, explode(',', $parameters[0]));
    }

    /**
     * Validate an array value has at least X values
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateMinCount($attribute, $value, $parameters = [])
    {
        if (empty($parameters[0])) {
            return true;
        }

        return is_array($value) && count($value) >= $parameters[0];
    }

    /**
     * Validate an array value has at most X values
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateMaxCount($attribute, $value, $parameters = [])
    {
        if (empty($parameters[0])) {
            return true;
        }

        return is_array($value) && count($value) <= $parameters[0];
    }

    /**
     * Validate a string attribute of an array value has a image file extension
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateImageAttribute($attribute, $value, $parameters = [])
    {
        if (! isset($parameters[0]) || ! isset($value[$parameters[0]])) {
            return false;
        }

        return !! preg_match('/\.(gif|jpg|jpeg|png|jpe)$/', $value[$parameters[0]]);
    }

    /**
     * Validate a directory path
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateIsDir($attribute, $value, $parameters = [])
    {
        return is_dir($value);
    }

    /**
     * Validate whether the specified value exists in the DB
     * OR if the value is 0 or '0'
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateExistsOrZero($attribute, $value, $parameters = [])
    {
        if ($value === '0' || $value === 0) {
            return true;
        }

        return $this->validateExists($attribute, $value, $parameters);
    }

    /**
     * Validate whether the specified values (in pipe delimited form) exist in the DB
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validatePipeExists($attribute, $value, $parameters = [])
    {
        if ($value) {
            $value = explode('|', $value);

            foreach ($value as $v) {
                if (! $this->validateExists($attribute, $v, $parameters)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validate whether the specified string has a certain length
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateLength($attribute, $value, $parameters = [])
    {
        $this->requireParameterCount(1, $parameters, 'length');

        if (! preg_match('/^\d+$/', $parameters[0])) {
            throw new \InvalidArgumentException('Validation rule length requires an integer value.');
        }

        return mb_strlen($value) === (int) $parameters[0];
    }

    /**
     * Replace all place-holders for the length rule.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function replaceLength($message, $attribute, $rule, $parameters)
    {
        return str_replace(':length', $parameters[0], $message);
    }

    /**
     * Validate an associative array values is found in the specified array
     *
     * 'your_field' => 'attribute_in:your_key,1,2,3'
     *
     * $data = ['your_field' => ['your_key' => '1']] // valid
     * $data = ['your_field' => ['your_key' => '4']] // not valid
     *
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateAttributeIn($attribute, $value, $parameters = [])
    {
        $attribute = array_shift($parameters);

        return isset($value[$attribute]) && in_array($value[$attribute], $parameters);
    }

    /**
     * Validate an associative array values is found in the specified array
     *
     * 'your_field' => 'nested_attribute_in:your_key,1,2,3'
     *
     * $data = ['your_field' => [['your_key' => '1'],['your_key' => '2']] // valid
     * $data = ['your_field' => [['your_key' => '4'],['your_key' => '2']] // not valid
     *
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateNestedAttributeIn($attribute, $value, $parameters = [])
    {
        $attribute = array_shift($parameters);

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $row) {
            if (! isset($row[$attribute]) || ! in_array($row[$attribute], $parameters)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate an associative array values is found in the specified array
     *
     * 'your_field' => 'nested_concatenated_attribute_in:your_key_a:your_key_b,ee1,gc2,s33'
     *
     * $data = ['your_field' => [['your_key_a' => 'ee','your_key_b' => '1'],['your_key_a' => 'gc','your_key_b' => '2']] // valid
     * $data = ['your_field' => [['your_key_a' => 's3','your_key_b' => '1'],['your_key_a' => 'gc','your_key_b' => '2']] // not valid
     *
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @return bool
     */
    public function validateNestedConcatenatedAttributeIn($attribute, $value, $parameters = [])
    {
        $attributes = explode(':', array_shift($parameters));

        if (! is_array($value)) {
            return true;
        }

        foreach ($value as $row) {
            $string = '';

            foreach ($attributes as $attribute) {
                if (! isset($row[$attribute])) {
                    return false;
                }

                $string .= $row[$attribute];
            }

            if (! in_array($string, $parameters)) {
                return false;
            }
        }

        return true;
    }
}
