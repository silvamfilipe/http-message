<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Validator;

/**
 * Validates if a provided value is a valid HTTP request message method
 *
 * @package Fsilva\HttpMessage\Validator
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HttpRequestMethod implements ValidatorInterface
{

    /**
     * @var mixed The value that will be checked
     */
    private $value;

    /**
     * @var array Available HTTP request methods
     */
    private $methodList = [
        'GET', 'POST', 'PUT', 'DELETE', 'HEAD',
        'OPTIONS', 'TRACE', 'CONNECT'
    ];

    /**
     * Checks if current value is a valid HTTP request message method
     *
     * @return bool
     */
    public function isValid()
    {
        $valid = false;
        foreach ($this->methodList as $method) {
            if (strtoupper($this->value) == $method) {
                $valid = true;
                break;
            }
        }
        return $valid;
    }

    /**
     * Sets the value to be validated
     *
     * @param mixed $value The value to be validated
     *
     * @return HttpRequestMethod A self instance allowing other method
     *   call chaining
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
