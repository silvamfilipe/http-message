<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage;

use Fsilva\HttpMessage\Validator\ValidatorInterface;
use Fsilva\HttpMessage\Exception\InvalidArgumentException;

/**
 * Static validator class utility and factory.
 *
 * This class is useful to simplify the code that needs to be written to
 * check a validation on a given value.
 *
 * @package Fsilva\HttpMessage
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class Validator
{

    /**
     * @var array List of known validators and its alias
     */
    private static $knownValidators = [
        'hotsname' => 'Validator\\HostName',
        'url' => 'Validator\\Url',
        'httpMethod' => 'Validator\\HttpRequestMethod',
    ];

    /**
     * Check if the provided value is valid using the given validator
     * class name or alias.
     *
     * This factory method cam be used with known validator alias. You may
     * use one of the following strings:
     *  - "hotsname" => Check if the provided value is a valid hostname;
     *  - "url" => Check if the provided value is a valid URL;
     *  - "httpMethod" => Check if the provided value is a valid HTTP
     *    request message method;
     *
     * If a class name is given then it must exists and be an implementation
     * of Fsilva\HttpMessage\Validator\ValidatorInterface interface.
     *
     * @see Fsilva\HttpMessage\Validator\ValidatorInterface
     *
     * @param string $validator The validator class name or alias
     * @param mixed  $value     The value that will be validated
     *
     * @return bool True if value passes validator validation call or
     *  false otherwise.
     *
     * @throws InvalidArgumentException If the validator name is not in the
     * known validator alias or, if the $validator provided is a class name,
     * the class does not exits nor implements the
     * Fsilva\HttpMessage\Validator\ValidatorInterface interface.
     */
    public static function isValid($validator, $value)
    {
        $validator = self::getValidatorObject($validator);
        return $validator->setValue($value)->isValid();
    }

    /**
     * Creates the validator object for the provided class name or alias
     *
     * @param string $validator Validator class name or alias
     *
     * @return ValidatorInterface The validator instance
     *
     * @throws InvalidArgumentException If the validator name is not in the
     * known validator alias or, if the $validator provided is a class name,
     * the class does not exits nor implements the Fsilva\HttpMessage\Validator
     * validator interface.
     */
    private static function getValidatorObject($validator)
    {
        $class = null;
        $isAlias = false;
        if (isset(self::$knownValidators[$validator])) {
            $name = self::$knownValidators[$validator];
            $class = __NAMESPACE__ . "\\{$name}";
            $isAlias = true;
        }

        if (is_null($class) && !class_exists($validator)) {
            throw new InvalidArgumentException(
                "Unknown validator class. Cannot instantiate '{$validator}'"
            );
        }

        $validatorObject = $isAlias ? new $class : new $validator;
        if (!($validatorObject instanceof ValidatorInterface)) {
            $itf = 'Fsilva\HttpMessage\Validator';
            throw new InvalidArgumentException(
                "'{$validator}' does not implements {$itf}"
            );
        }

        return $validatorObject;
    }
}
