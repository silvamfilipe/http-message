<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Parser;

use Psr\Http\Message\StreamableInterface;
use Fsilva\HttpMessage\Exception\MissingContentException;
use Fsilva\HttpMessage\Exception\ParsingFailureException;

/**
 * Parsers are used to parse body data
 *
 * @package Fsilva\HttpMessage\Parser
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface ParserInterface
{

    /**
     * Parses the current content and returns its data
     *
     * @return null|array|object The deserialized data from current content
     *
     * @throws ParsingFailureException If an error occurs when parsing the
     *                                 contents
     * @throws MissingContentException If trying to parse content without
     *                                 setting the content stream
     */
    public function parse();

    /**
     * Sets the content to be parsed
     *
     * @param StreamableInterface $content
     *
     * @return self
     */
    public function setContent(StreamableInterface $content);
}