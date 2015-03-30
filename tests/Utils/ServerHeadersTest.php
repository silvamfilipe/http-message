<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests\Utils;

use Fsilva\HttpMessage\ServerRequest as Request;
use Fsilva\HttpMessage\Utils\ServerHeaders;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ServerHeadersTest
 *
 * @package Fsilva\HttpMessage\Tests\Stream
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ServerHeadersTest extends TestCase
{
    private $tmp;

    protected function setup()
    {
        parent::setUp();
        $this->tmp = $_SERVER;
        $_SERVER = $this->serverData;
    }

    protected function tearDown()
    {
        $_SERVER = $this->tmp;
        parent::tearDown();
    }

    public function testHeaderReader()
    {
        $headers = ServerHeaders::get(new Request());
        $this->assertTrue(is_array($headers));
        $this->assertEquals($this->expected, $headers);
    }

    public function testApacheAuthorization()
    {
        $this->expected['Authorization'] = ['NTLM LAKSJDAOISUDAÇLKSJDAOISDJO'];
        $factory = new ServerHeaders(new Request());
        $factory->setHeadersCallback(function(){
            return self::getHeadersMock();
        });
        $factory->normalizeServer();
        $this->assertEquals($this->expected, $factory->getHeaders());
    }

    protected static function getHeadersMock()
    {
        return [
            'Authorization' => 'NTLM LAKSJDAOISUDAÇLKSJDAOISDJO'
        ];
    }

    public function testApacheAuthorizationSmallCaps()
    {
        $this->expected['Authorization'] = ['NTLM LAKSJDAOISUDAÇLKSJDAOISDJO'];
        $factory = new ServerHeaders(new Request());
        $factory->setHeadersCallback(function(){
            return self::getHeadersMockSmallCaps();
        });
        $factory->normalizeServer();
        $this->assertEquals($this->expected, $factory->getHeaders());
    }

    protected static function getHeadersMockSmallCaps()
    {
        return [
            'authorization' => 'NTLM LAKSJDAOISUDAÇLKSJDAOISDJO'
        ];
    }

    private $expected = [
        'Host' => ['www.example.com'],
        'User-Agent' => ['Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:36.0) Gecko/20100101 Firefox/36.0'],
        'Accept' => ['text/html', 'application/xhtml+xml', 'application/xml;q=0.9', '*/*;q=0.8'],
        'Accept-Language' => ['en-US', 'en;q=0.5'],
        'Accept-Encoding' => ['gzip', 'deflate'],
        'Dnt' => ['1'],
        'Connection' => ['keep-alive'],
        'Cache-Control' => ['max-age=0'],
        'Content-Type' => ['application/json'],
    ];

    /**
     * @var array SERVER data for tests
     */
    private $serverData = [
        'HTTP_HOST' => 'www.example.com',
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:36.0) Gecko/20100101 Firefox/36.0',
        'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.5',
        'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
        'HTTP_DNT' => '1',
        'HTTP_COOKIE' => '_ga=GA1.1.1216126857.1425322398; SLICKSID=cfhdp51sa117klsqgerfr85865',
        'HTTP_CONNECTION' => 'keep-alive',
        'HTTP_CACHE_CONTROL' => 'max-age=0',
        'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
        'SERVER_SIGNATURE' => 'Apache/2.4.10 (Ubuntu) Server at localhost Port 80',
        'SERVER_SOFTWARE' => 'Apache/2.4.10 (Ubuntu)',
        'SERVER_NAME' => 'www.example.com',
        'SERVER_ADDR' => '127.0.0.1',
        'SERVER_PORT' => '80',
        'REMOTE_ADDR' => '127.0.0.1',
        'DOCUMENT_ROOT' => '/var/www/html',
        'REQUEST_SCHEME' => 'http',
        'CONTEXT_PREFIX' => '',
        'CONTEXT_DOCUMENT_ROOT' => '/var/www/html',
        'SERVER_ADMIN' => 'webmaster@localhost',
        'SCRIPT_FILENAME' => '/var/www/html/projects/SafetyReports/webroot/test.php',
        'REMOTE_PORT' => '51768',
        'GATEWAY_INTERFACE' => 'CGI/1.1',
        'SERVER_PROTOCOL' => 'HTTP/1.1',
        'REQUEST_METHOD' => 'GET',
        'QUERY_STRING' => '',
        'REQUEST_URI' => '/projects/SafetyReports/webroot/test.php',
        'SCRIPT_NAME' => '/projects/SafetyReports/webroot/test.php',
        'PHP_SELF' => '/projects/SafetyReports/webroot/test.php',
        'REQUEST_TIME_FLOAT' => '1426678314.79',
        'REQUEST_TIME' => '1426678314',
        'CONTENT_TYPE' => 'application/json'
    ];
}
