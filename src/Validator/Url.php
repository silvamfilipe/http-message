<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Validator;

/**
 * Class Url
 *
 * @package Fsilva\HttpMessage\Validator
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Url implements ValidatorInterface
{

    /**
     * @var mixed Value to evaluate
     */
    private $value;

    /**
     * Checks if current value is valid
     *
     * @return bool
     */
    public function isValid()
    {
        $valid = filter_var($this->value, FILTER_VALIDATE_URL);
        return $valid;
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
}
