<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Validator;

/**
 * Simple validation interface that represents a validator behavior.
 *
 * @package Fsilva\HttpMessage\Validator
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface ValidatorInterface {

    /**
     * Checks if current value is valid
     *
     * @return bool
     */
    public function isValid();

    /**
     * Sets the value to be validated
     *
     * @param mixed $value The value to be validated
     *
     * @return self A self instance allowing other method call chaining
     */
    public function setValue($value);
}
