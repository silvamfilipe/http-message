<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Validator;


/**
 * This validator checks if the value is string or an array of strings
 *
 * Its used in the message header assignment to validate that on a string or
 * an array of string can be used to create or change a header.
 *
 * @see Fsilva\HttpMessage\Message::checkHeaderNameAndValue()
 *
 * @package Fsilva\HttpMessage\Validator
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class HeaderValue implements ValidatorInterface
{

    /**
     * @var string|string[]
     */
    private $value;

    /**
     * Checks if current value is valid
     *
     * @return bool
     */
    public function isValid()
    {
        $allStrings = true;

        if (!is_string($this->value) && !is_array($this->value)) {
            $allStrings = false;
        }

        return $allStrings !== false && $this->checkAllStrings($this->value);
    }

    /**
     * Sets the value to be validated
     *
     * @param mixed $value The value to be validated
     *
     * @return self A self instance allowing other method call chaining
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Check if all values in the provided array are string
     *
     * if a string is passed as argument it will be converted to a single
     * element array so it can pass as valid
     *
     * @param string|array $value
     *
     * @return bool True if all array elements are strings, false otherwise.
     */
    private function checkAllStrings($value)
    {
        $allStrings = true;
        $value = is_string($value) ? [$value] : $value;

        array_walk($value, function($element) use (&$allStrings) {
            if (!is_string($element)) {
                $allStrings = false;
            }
        });

        return $allStrings;
    }
}