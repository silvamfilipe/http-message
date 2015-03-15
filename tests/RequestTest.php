<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests;

use Fsilva\HttpMessage\Request;
use Fsilva\HttpMessage\Uri;
use PHPUnit_Framework_TestCase;

/**
 * Request test case
 *
 * @package Fsilva\HttpMessage\Tests
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * A list of valid HTTP request message methods
     *
     * @return array
     */
    public function validMethods()
    {
        return [
            'get'     => [Request::METHOD_GET],
            'post'    => [Request::METHOD_POST],
            'put'     => [Request::METHOD_PUT],
            'delete'  => [Request::METHOD_DELETE],
            'options' => [Request::METHOD_OPTIONS],
            'head'    => [Request::METHOD_HEAD],
            'connect' => [Request::METHOD_CONNECT],
            'trace'   => [Request::METHOD_TRACE],
        ];
    }

    /**
     * Invalid HTTP request message methods
     * @return array
     */
    public function invalidMethods()
    {
        return [
            ['some text'],
            [23],
            ['0909'],
            ['POSTAL']
        ];
    }

    /**
     * @dataProvider validMethods
     * @param string $method
     */
    public function testCreationWithMethod($method)
    {
        $request = new Request();
        $new = $request->withMethod($method);
        $this->validateMutation($request, $new);
        $this->assertEquals($method, $new->getMethod());
    }

    /**
     * @dataProvider invalidMethods
     */
    public function testCreationWithInvalidMethod($method)
    {
        $request = new Request();
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidArgumentException'
        );
        $request->withMethod($method);
    }

    /**
     * Checks the immutability of tow given request objects
     *
     * @param Request $req
     * @param Request $new
     */
    protected function validateMutation($req, $new)
    {
        $this->assertNotSame($req, $new);
        $class = 'Psr\\Http\\Message\\RequestInterface';
        $this->assertInstanceOf($class, $new);
        $this->assertInstanceOf($class, $req);
    }

    public function testCreateWithUri()
    {
        $request = new Request();
        $uri = new Uri('http://example.com/users/search?name=john');
        $new = $request->withUri($uri);
        $this->validateMutation($request, $new);
        $this->assertSame($uri, $new->getUri());
    }

    public function testCreateWithTarget()
    {
        $request = new Request();
        $this->assertEquals('/', $request->getRequestTarget());
        $new = $request->withRequestTarget('/some/path');
        $this->validateMutation($request, $new);
        $this->assertEquals('/some/path', $new->getRequestTarget());
        $uri = new Uri('http://example.com/users/search?name=john');
        $new = $request->withUri($uri);
        $this->validateMutation($request, $new);
        $this->assertEquals(
            '/users/search?name=john',
            $new->getRequestTarget()
        );
    }
}
