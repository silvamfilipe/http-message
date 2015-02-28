<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Exception;

use Fsilva\HttpMessage\Exception;
use LogicException;

/**
 * An InvalidArgumentException exception is thrown when a tentative to call
 * a method has an invalid (wrong type or value) argument.
 *
 * The exception message should help to find out what argument is invalid.
 *
 * @package Fsilva\HttpMessage\Exception
 * @author  Filipe Silva
 */
class InvalidArgumentException extends LogicException implements Exception
{

}
