<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Parser;

use Fsilva\HttpMessage\Stream;

/**
 * Used by factory when it cannot realize that parser to create
 *
 * @package Fsilva\HttpMessage\Parser
 */
class NullParser extends AbstractParser implements ParserInterface
{

    /**
     * Parses the current content and returns its data
     *
     * @return null The deserialized data from current content*
     */
    public function parse()
    {
        return null;
    }

}