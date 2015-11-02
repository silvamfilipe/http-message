<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Exception;

use Fsilva\HttpMessage\Exception;
use \InvalidArgumentException as LogicException;

/**
 * This exception is thrown when a tentative of creating an URI with an
 * invalid or unsupported scheme occurs.
 *
 * @package Fsilva\HttpMessage\Exception
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class InvalidSchemeException extends LogicException implements Exception
{

}
