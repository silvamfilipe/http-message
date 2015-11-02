<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Validator;


/**
 * Key Value Array checks if the provided value is an associative array where
 * keys and values are strings.
 *
 * @package Fsilva\HttpMessage\Validator
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class KeyValueArray implements ValidatorInterface
{
    /**
     * @var mixed the value to be checked
     */
    private $value;

    /**
     * Checks if current value is valid
     *
     * @return bool
     */
    public function isValid()
    {
        if (!is_array($this->value)) {
            return false;
        }

        $valid = true;
        array_walk($this->value, function($element, $key) use (&$valid) {
            if (!is_string($element)) {
                $valid = false;
            }
            if (!is_string($key)) {
                $valid = false;
            }
        });
        return $valid;
    }

    /**
     * Sets the value to be validated
     *
     * @param mixed $value The value to be validated
     *
     * @return KeyValueArray A self instance allowing method call chaining
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
