<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests\PhpEnvironment;

use Fsilva\HttpMessage\PhpEnvironment\Request;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * RequestTest test case
 *
 * @package Fsilva\HttpMessage\Tests\PhpEnvironment
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RequestTest extends TestCase 
{

    /**
     * Server parameter retrieve
     */
    public function testServerParamRetrieve()
    {
        $_SERVER['__test__'] = 'This is a test';
        $request = new Request();
        $this->assertEquals('This is a test', $request->getServer('__test__'));
        $this->assertTrue($request->getServer('unknown', true));
    }

    /**
     * Cookie parameter retrieve
     */
    public function testCookieParamRetrieve()
    {
        $_COOKIE['__test__'] = 'This is a test';
        $request = new Request();
        $this->assertEquals('This is a test', $request->getCookie('__test__'));
        $this->assertTrue($request->getCookie('unknown', true));
    }

    public function testQueryParamRetrieve()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['__test__'] = 'This is a test';
        $request = new Request();
        $this->assertEquals('This is a test', $request->getQuery('__test__'));
        $this->assertTrue($request->getQuery('unknown', true));
        $this->assertTrue($request->isGet());
        $this->assertEquals($_GET, $request->getQuery());
    }

    public function testPostParamRetrieve()
    {
        $srv = $_SERVER;
        $post = $_POST;

        $_SERVER['HTTP_HOST'] = 'www.example.com';
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST['__test__'] = 'This is a test';
        $request = new Request();
        $this->assertEquals('This is a test', $request->getPost('__test__'));
        $this->assertTrue($request->getPost('unknown', true));
        $this->assertTrue($request->isPost());
        $this->assertEquals($_POST, $request->getPost());

        $_POST = $post;
        $_SERVER = $srv;
    }


    public function testGetFilesMetaData()
    {
        $data = $this->filesExample()['simple'][0];
        $_FILES = $data;
        $request = new Request();
        $this->assertEquals($data['field2'], $request->getFiles('field2'));
    }

    public function testCallToUndefinedMethod()
    {
        $request = new Request();
        $this->setExpectedException('\BadMethodCallException');
        $request->foo();
    }

    public function testGetBasePath()
    {
        $srv = $_SERVER;
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['QUERY_STRING'] = 'url=users/read/2&extension=&page=3';
        $_SERVER['REQUEST_URI'] = '/projects/Experimental/HttpMessages/sandbox/users/read/2?page=3';
        $_SERVER['SCRIPT_NAME'] = '/projects/Experimental/HttpMessages/sandbox/index.php';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/html/projects/Experimental/HttpMessages/sandbox/index.php';
        $_SERVER['PHP_SELF'] = '/projects/Experimental/HttpMessages/sandbox/index.php';

        $request = new Request();
        $basePath = '/projects/Experimental/HttpMessages/sandbox';
        $this->assertEquals($basePath, $request->getBasePath());

        $_SERVER = $srv;
    }

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
}
