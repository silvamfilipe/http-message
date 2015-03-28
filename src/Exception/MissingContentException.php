<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Exception;

use LogicException;
use Fsilva\HttpMessage\Exception;

/**
 * Exception thrown when a trying to parse content on a parse object without
 * setting the content stream.
 *
 * @package Fsilva\HttpMessage\Exception
 */
class MissingContentException extends LogicException implements Exception
{

}