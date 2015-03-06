<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests;

use Fsilva\HttpMessage\Uri;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * UriTest test case
 *
 * @package Fsilva\HttpMessage\Tests
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class UriTest extends TestCase 
{

    /**
     * Invalid schemes
     * @return array
     */
    public function invalidSchemes()
    {
        return [
            'ftp' => ['ftp://'],
            'ssh' => ['ssh://'],
            'object' => [new \stdClass()],
            'boolean' => [true],
            'integer' => [25]
        ];
    }

    /**
     * Sets creation with invalid schemes
     *
     * @dataProvider invalidSchemes
     * @param mixed $scheme
     */
    public function testCreateWithScheme($scheme)
    {
        $uri = new Uri();
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidSchemeException'
        );
        $newUri = $uri->withScheme($scheme);
    }
}
