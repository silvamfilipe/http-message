<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Validator;

/**
 * Class HostName
 *
 * @package Fsilva\HttpMessage\Validator
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class HostName implements ValidatorInterface
{

    /**
     * @var string The value being validate
     */
    private $value;

    /**
     * Checks if current value is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isValidHostname();
    }

    /**
     * Sets the value to be validated as an hostname
     *
     * @param mixed $value The value to be validated
     *
     * @return HostName A self instance allowing other method call chaining
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Checks if current hostname is valid
     *
     * @return bool True if it a valid hostname, false otherwise
     */
    private function isValidHostname()
    {
        $validChar = '/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i';
        $overallLength = '/^.{1,253}$/';
        $labelLength = '/^[^\.]{1,63}(\.[^\.]{1,63})*$/';

        $valid = (
            (bool) preg_match($validChar, $this->value) &&
            (bool) preg_match($overallLength, $this->value) &&
            (bool) preg_match($labelLength, $this->value)
        );

        return $valid;
    }
}
