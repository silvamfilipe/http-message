<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Exception;

use RuntimeException;
use Fsilva\HttpMessage\Exception;

/**
 * This exception is thrown when a parse object fails to parse its contents
 *
 * @package Fsilva\HttpMessage\Exception
 */
class ParsingFailureException extends RuntimeException implements Exception
{

}