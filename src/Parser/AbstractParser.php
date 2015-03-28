<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Parser;

use Psr\Http\Message\StreamableInterface;


/**
 * Abstract parser class where all common login will live in
 *
 * @package Fsilva\HttpMessage\Parser
 */
abstract class AbstractParser implements ParserInterface
{

    /**
     * @var StreamableInterface The content stream
     */
    protected $content;

    /**
     * Sets the content to be parsed
     *
     * @param StreamableInterface $content
     *
     * @return self
     */
    public function setContent(StreamableInterface $content)
    {
        $this->content = $content;
        return $this;
    }
}