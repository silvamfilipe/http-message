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

    public function testUserInfo()
    {
        $uri = new Uri();
        $new = $uri->withUserInfo('foo', 'pass');
        $this->checkMutation($uri, $new);
        $this->assertEquals('foo:pass', $new->getUserInfo());

        $other = $new->withUserInfo('user');
        $this->assertEquals('user', $other->getUserInfo());
    }

    public function testGetAuthority()
    {
        $uri = new Uri();
        $uri = $uri->withHost('example.com');

        $this->assertEquals('example.com', $uri->getAuthority());

        $uri = $uri->withUserInfo('user-name');
        $this->assertEquals('user-name@example.com', $uri->getAuthority());

        $uri = $uri->withUserInfo('user-name', 'password');
        $this->assertEquals('user-name:password@example.com', $uri->getAuthority());

        $uri = $uri->withPort('8080');
        $this->assertEquals('user-name:password@example.com:8080', $uri->getAuthority());

    }

    public function testPath()
    {
        $uri = new Uri();
        $new = $uri->withPath('/here/his/my/path/');
        $this->checkMutation($uri, $new);
        $this->assertEquals('/here/his/my/path', $new->getPath());
        $uri = $new->withPath('');
        $this->assertEquals('', $uri->getPath());
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidArgumentException'
        );
        $uri->withPath('/here are/some errors');
    }

    public function testQuery()
    {
        $uri = new Uri();
        $new = $uri->withQuery('?probably=this&is=not');
        $this->checkMutation($uri, $new);
        $this->assertEquals('probably=this&is=not', $new->getQuery());

        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidArgumentException'
        );
        $uri->withQuery(new \stdClass());
    }

    public function testFragment()
    {
        $uri = new Uri();
        $new = $uri->withFragment('#fragment');
        $this->checkMutation($uri, $new);
        $this->assertEquals('fragment', $new->getFragment());
    }

    public function testToStringOutput()
    {
        $str = "http://user:pass@example.com:8080/example/path.html?some=query#segment";
        $uri = new Uri($str);
        $this->assertEquals($str, (string) $uri);

        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidArgumentException'
        );
        new Uri("Some invalid url");
    }
}
