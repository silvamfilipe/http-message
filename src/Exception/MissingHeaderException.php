<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Exception;

use RuntimeException;
use Fsilva\HttpMessage\Exception;

/**
 * A MissingHeaderException exception is thrown whe a tentative to retrieve
 * the value of an header that does not exists int the HTTP message.
 *
 * @package Fsilva\HttpMessage\Exception
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class MissingHeaderException extends RuntimeException implements Exception
{

}
