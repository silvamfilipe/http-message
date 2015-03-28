<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests;

use Fsilva\HttpMessage\PhpEnvironment\Request;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * ServerRequestTest test case
 *
 * @package Fsilva\HttpMessage\Tests
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ServerRequestTest extends TestCase 
{

    /**
     * @return array 2 examples of $_FILES superglobal
     */
    public function filesExample()
    {
        return [
            'simple' => [
                [
                    'field1' => [
                        'name' => 'MyFile.jpg',
                        'type' => 'image/jpeg',
                        'tmp_name' => '/tmp/php/php6hst32',
                        'error' => UPLOAD_ERR_OK,
                        'size' => 98174
                    ],
                    'field2' => [
                        'name' => 'MyFile.jpg',
                        'type' => 'image/jpeg',
                        'tmp_name' => '/tmp/php/php6hst32',
                        'error' => UPLOAD_ERR_OK,
                        'size' => 98174
                    ]
                ]
            ],
            'multiple' => [
                [
                    'field' => [
                        'name' => ['MyFile1.jpg', 'MyFile2.jpg'],
                        'type' => ['image/jpeg', 'image/jpeg'],
                        'tmp_name' => [
                            '/tmp/php/php6hst32',
                            '/tmp/php/php6hst33'
                        ],
                        'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_OK],
                        'size' => [98174, 87987]
                    ]
                ]
            ]
        ];
    }

    /**
     * Invalid cookies data provider
     */
    public function invalidCookies()
    {
        return [
            'invalid array' => [['test']],
            'object' => [[new \stdClass()]],
            'number' => [[0]],
            'null' => [[null]],
            'boolean' => [[true]],
            'invalid key' => [[23 => 'test']]
        ];
    }

    /**
     * Valid cookies data provider
     */
    public function validCookies()
    {
        return [
            'simple' => [['key' => 'value']],
            'hashed' => [['key' => md5('value'), 'other' => md5('test')]],
        ];
    }

    /**
     * Invalid parsed data
     *
     * @return array
     */
    public function invalidParsedBodyData()
    {
        return [
            'boolean' => [true],
            'integer' => [23],
            'string' => ['a test'],
            'float' => [123.09]
        ];
    }

    /**
     * Valid parsed data
     */
    public function validParsedData()
    {
        return [
            'null' => [null],
            'array' => [['foo' => 'bar', 'baz' => 'boo']],
            'object' => [(object)['foo' => 'bar', 'baz' => 'boo']]
        ];
    }

    /**
     * Test creation with invalid cookie parameters
     * @param mixed $params
     * @test
     * @dataProvider invalidCookies
     */
    public function invalidRequestWithCookieParams($params)
    {
        $request = new Request();
        $this->setExpectedException(
            "Fsilva\\HttpMessage\\Exception\\InvalidArgumentException"
        );
        $request->withCookieParams($params);
    }

    /**
     * Checking method recognition on server request
     */
    public function testRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $request = new Request();
        $this->assertEquals('DELETE', $request->getMethod());
    }

    /**
     * Test creation with valid cookie parameters
     * @dataProvider validCookies
     * @param array $params
     */
    public function testRequestWithCookieParams($params)
    {
        $request = new Request();
        $new = $request->withCookieParams($params);
        $this->assertEquals($params, $new->getCookieParams());
        $this->assertNotEquals($params, $request->getCookieParams());
        $this->checkImmutability($request, $new);
    }

    /**
     * Check invalid request creation with bab query parameters
     * @dataProvider invalidCookies
     * @param mixed $query
     */
    public function testRequestWithInvalidQuery($query)
    {
        $request = new Request();
        $this->setExpectedException(
            "Fsilva\\HttpMessage\\Exception\\InvalidArgumentException"
        );
        $request->withQueryParams($query);
    }

    /**
     * Check request creation with query parameters
     * @dataProvider validCookies
     * @param array $query
     */
    public function testRequestWithValidQuery($query)
    {
        $request = new Request();
        $new = $request->withQueryParams($query);
        $this->checkImmutability($request, $new);
        $this->assertEquals($query, $new->getQueryParams());
    }

    public function testFilesRetrieval()
    {
        foreach ($this->filesExample() as $params) {
            $_FILES = $params;
            $request = new Request();
            $this->assertEquals($params, $request->getFileParams());
        }

    }

    /**
     * @dataProvider invalidParsedBodyData
     * @param $data
     */
    public function testInvalidParsedBodyData($data)
    {
        $request = new Request();
        $this->setExpectedException(
            "Fsilva\\HttpMessage\\Exception\\InvalidArgumentException"
        );
        $request->withParsedBody($data);
    }

    /**
     * @dataProvider validParsedData
     * @param $data
     */
    public function testCreateWithParsedBody($data)
    {
        $request = new Request();
        $new = $request->withParsedBody($data);
        $this->checkImmutability($request, $new);
        $this->assertEquals($data, $new->getParsedBody());
        $this->assertNotEquals($data, $request);
    }

    public function testParsedBody()
    {
        $srv = $_SERVER;
        $post = $_POST;

        $_SERVER['HTTP_HOST'] = 'www.example.com';
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['foo' => 'bar', 'baz' => 'boo'];

        $request = new Request();
        $this->assertEquals($_POST, $request->getParsedBody());

        $_POST = $post;
        $_SERVER = $srv;
    }

    protected function checkImmutability($request, $new)
    {
        $type = "Fsilva\\HttpMessage\\ServerRequest";
        $interface = "Psr\\Http\\Message\\ServerRequestInterface";
        $this->assertInstanceOf($type, $request);
        $this->assertInstanceOf($interface, $request);
        $this->assertInstanceOf($interface, $new);
        $this->assertNotSame($request, $new);
    }
}
