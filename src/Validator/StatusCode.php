<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Validator;

use Fsilva\HttpMessage\Response;

/**
 * Class StatusCode
 *
 * @package Fsilva\HttpMessage\Validator
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class StatusCode implements ValidatorInterface
{
    /**
     * @var mixed The value to check
     */
    private $value;

    /**
     * Checks if current value is valid
     *
     * @return bool
     */
    public function isValid()
    {
        $valid = true;
        if (!is_integer($this->value)) {
            $valid = false;
        }
        return $valid !== false && array_key_exists(
            $this->value,
            Response::getRecommendedReasonPhrases()
        );
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