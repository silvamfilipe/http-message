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
     * Valid schemes
     * @return array
     */
    public function validSchemes()
    {
        return [
            'http' => ['http://'],
            'https' => ['https://'],
            'empty' => [''],
            'upper' => ['HTTP://'],
        ];
    }

    /**
     * invalid host names
     * @return array
     */
    public function invalidHostnames()
    {
        return [
            ['goo gle.com'], ['google..com' ], ['google.com '],
            ['google-.com'], ['.google.com'], ['<script'],
            ['alert('], ['.'], ['..'], [' '], ['-'],
        ];
    }

    /**
     * Valid host names
     * @return array
     */
    public function validHostNames()
    {
        return [
            [''], ['a'], [0], ['a.b'], ['localhost'],
            ['google.com'], ['news.google.co.uk'],
            ['xn--fsqu00a.xn--0zwm56d']
        ];
    }

    /**
     * Valid URI ports
     *
     * @return array
     */
    public function validPorts()
    {
        return [
            ['8080'], [8443], ['6666'], [80], [443], ['']
        ];
    }

    /**
     * @return array
     */
    public function invalidPorts()
    {
        return [
            [-1], ['5'], [64535], [1023], [0]
        ];
    }

    /**
     * Sets creation with invalid schemes
     *
     * @dataProvider invalidSchemes
     * @param mixed $scheme
     */
    public function testCreateWithInvalidScheme($scheme)
    {
        $uri = new Uri();
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidSchemeException'
        );
        $uri->withScheme($scheme);
    }

    /**
     * Sets creation with valid schemes
     *
     * @dataProvider validSchemes
     * @param mixed $scheme
     */
    public function testCreateWithScheme($scheme)
    {
        $uri = new Uri();
        $newUri = $uri->withScheme($scheme);
        $this->checkMutation($uri, $newUri);

        $expected = strtolower(str_replace('://', '', $scheme));
        $this->assertEquals($expected, $newUri->getScheme());
    }

    /**
     * Test creation with invalid host names
     * @dataProvider invalidHostnames
     * @param mixed $hostname
     */
    public function testCreateWithInvalidHostnames($hostname)
    {
        $uri = new Uri();
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidHostNameException'
        );
        $uri->withHost($hostname);
    }

    /**
     * Test creation with valid host names
     * @dataProvider validHostnames
     * @param mixed $hostname
     */
    public function testCreationValidHostnames($hostname)
    {
        $uri = new Uri();
        $newUri = $uri->withHost($hostname);
        $this->checkMutation($uri, $newUri);
        $this->assertEquals($hostname, $newUri->getHost());
    }

    /**
     * Test creation with invalid port
     * @dataProvider invalidPorts
     * @param int $port
     */
    public function testCreateWithInvalidPort($port)
    {
        $uri = new Uri();
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidArgumentException'
        );
        $uri->withPort($port);
    }

    /**
     * Test creation with port
     *
     * @dataProvider validPorts
     * @param int $port
     */
    public function testCreationWithPort($port)
    {
        $uri = new Uri();
        $newUri = $uri->withPort($port);
        $this->checkMutation($uri, $newUri);
        if ($port == 80) {
            $newUri = $newUri->withScheme('http');
            $port = null;
        }

        if ($port == 443) {
            $newUri = $newUri->withScheme('https');
            $port = null;
        }
        $this->assertEquals($port, $newUri->getPort());
    }

    /**
     * Check mutation state
     *
     * @param Uri $old
     * @param Uri $new
     */
    protected function checkMutation($old, $new)
    {
        $this->assertInstanceOf('Fsilva\\HttpMessage\\Uri', $new);
        $this->assertInstanceOf('Psr\\Http\\Message\\UriInterface', $new);
        $this->assertNotSame($old, $new);
    }
}
