<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Parser;

use Fsilva\HttpMessage\Stream;
use Psr\Http\Message\StreamableInterface;

/**
 * Url Encoded Post parser
 *
 * It will only return the $_POST superglobal as PHP does the parsing already
 *
 * @package Fsilva\HttpMessage\Parser
 */
class UrlEncodedPost extends AbstractParser implements ParserInterface
{

    /**
     * Parses the current content and returns its data
     *
     * @return null|array|object The deserialized data from current content
     */
    public function parse()
    {
       return $_POST;
    }

}