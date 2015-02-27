<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Exception;

use LogicException;
use Fsilva\HttpMessage\Exception;

/**
 * An InvalidVersionException is thrown
 *
 * @package Fsilva\HttpMessage\Exception
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class InvalidVersionException extends LogicException implements Exception
{

}